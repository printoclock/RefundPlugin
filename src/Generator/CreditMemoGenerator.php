<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethod ;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoBillingData;
use Sylius\RefundPlugin\Entity\CreditMemoChannel;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CreditMemoPaymentMethod;
use Sylius\RefundPlugin\Exception\InvoiceNotFound;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Exception\PaymentMethodNotFound;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;

final class CreditMemoGenerator implements CreditMemoGeneratorInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var CreditMemoUnitGeneratorInterface */
    private $orderItemUnitCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $shipmentCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $feeCreditMemoUnitGenerator;

    /** @var NumberGenerator */
    private $creditMemoNumberGenerator;

    /** @var CurrentDateTimeProviderInterface */
    private $currentDateTimeProvider;

    /** @var CreditMemoIdentifierGeneratorInterface */
    private $uuidCreditMemoIdentifierGenerator;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InvoiceRepository $invoiceRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $feeCreditMemoUnitGenerator,
        NumberGenerator $creditMemoNumberGenerator,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        CreditMemoIdentifierGeneratorInterface $uuidCreditMemoIdentifierGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->orderItemUnitCreditMemoUnitGenerator = $orderItemUnitCreditMemoUnitGenerator;
        $this->shipmentCreditMemoUnitGenerator = $shipmentCreditMemoUnitGenerator;
        $this->feeCreditMemoUnitGenerator = $feeCreditMemoUnitGenerator;
        $this->creditMemoNumberGenerator = $creditMemoNumberGenerator;
        $this->currentDateTimeProvider = $currentDateTimeProvider;
        $this->uuidCreditMemoIdentifierGenerator = $uuidCreditMemoIdentifierGenerator;
    }

    public function generate(
        string $token,
        string $orderNumber,
        int $total,
        array $units,
        array $shipments,
        array $fees,
        string $comment,
        int $paymentMethodId
    ): CreditMemoInterface {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        if ($order === null) {
            throw OrderNotFound::withNumber($orderNumber);
        }

        /** @var Invoice|null $invoice */
        $invoice = $this->invoiceRepository->getOneByOrderNumber($orderNumber);

        if ($invoice === null) {
            throw InvoiceNotFound::withNumber($orderNumber);
        }

        /** @var PaymentMethod|null $invoice */
        $paymentMethod = $this->paymentMethodRepository->find($paymentMethodId);

        if ($paymentMethod === null) {
            throw PaymentMethodNotFound::withNumber($orderNumber);
        }

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $creditMemoUnits = [];

        /** @var UnitRefundInterface $unit */
        foreach ($units as $unit) {
            $creditMemoUnits[] = $this->orderItemUnitCreditMemoUnitGenerator
                ->generate($unit->id(), $unit->total())
                ->serialize();
        }

        /** @var UnitRefundInterface $shipment */
        foreach ($shipments as $shipment) {
            $creditMemoUnits[] = $this->shipmentCreditMemoUnitGenerator
                ->generate($shipment->id(), $shipment->total())
                ->serialize();
        }

        /** @var UnitRefundInterface $fee */
        foreach ($fees as $fee) {
            $creditMemoUnits[] = $this->feeCreditMemoUnitGenerator
                ->generate($fee->id(), $fee->total(), $order->getItemUnits()->first())
                ->serialize();
        }

        $invoiceBillingData = $invoice->billingData();
        $billingData = new CreditMemoBillingData(
            $invoiceBillingData->firstName(),
            $invoiceBillingData->lastName(),
            null,
            null,
            $invoiceBillingData->street(),
            $invoiceBillingData->postcode(),
            $invoiceBillingData->city(),
            $invoiceBillingData->countryCode()
        );

        $invoiceShopBillingData = $invoice->shopBillingData();
        $shopBillingData = new CreditMemoBillingData(
            null,
            null,
            $invoiceShopBillingData->getCompany(),
            $invoiceShopBillingData->getTaxId(),
            $invoiceShopBillingData->getStreet(),
            $invoiceShopBillingData->getPostcode(),
            $invoiceShopBillingData->getCity(),
            $invoiceShopBillingData->getCountryCode()
        );

        $paymentMethodData = new CreditMemoPaymentMethod(
            $paymentMethod->getCode(),
            $paymentMethod->getName(),
            $paymentMethod->getInstructions(),
            null,
            null
        );

        return new CreditMemo(
            $this->uuidCreditMemoIdentifierGenerator->generate(),
            $token,
            $this->creditMemoNumberGenerator->generate(),
            $orderNumber,
            $total,
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            new CreditMemoChannel($channel->getCode(), $channel->getName(), $channel->getColor()),
            $creditMemoUnits,
            $comment,
            $this->currentDateTimeProvider->now(),
            $billingData->serialize(),
            $shopBillingData->serialize(),
            $paymentMethodData->serialize()
        );
    }
}
