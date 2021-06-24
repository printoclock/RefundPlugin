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
use Sylius\RefundPlugin\Entity\CreditMemoPayment;
use Sylius\RefundPlugin\Exception\InvoiceNotFound;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Exception\PaymentMethodNotFound;
use Sylius\RefundPlugin\Model\CustomRefund;
use Sylius\RefundPlugin\Model\FeeRefund;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\PaymentRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
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
    private $customCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $shipmentCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $paymentCreditMemoUnitGenerator;

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
        CreditMemoUnitGeneratorInterface $customCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $paymentCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $feeCreditMemoUnitGenerator,
        NumberGenerator $creditMemoNumberGenerator,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        CreditMemoIdentifierGeneratorInterface $uuidCreditMemoIdentifierGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->orderItemUnitCreditMemoUnitGenerator = $orderItemUnitCreditMemoUnitGenerator;
        $this->customCreditMemoUnitGenerator = $customCreditMemoUnitGenerator;
        $this->shipmentCreditMemoUnitGenerator = $shipmentCreditMemoUnitGenerator;
        $this->paymentCreditMemoUnitGenerator = $paymentCreditMemoUnitGenerator;
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
        array $customs,
        array $shipments,
        array $payments,
        array $fees,
        string $comment,
        int $paymentMethodId,
        int $amountFee = 0
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

        /** @var PaymentMethod|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->find($paymentMethodId);
        if ($paymentMethod === null) {
            throw PaymentMethodNotFound::withNumber($orderNumber);
        }

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $taxTotal = 0;
        $creditMemoUnits = [];

        /** @var OrderItemUnitRefund $unit */
        foreach ($units as $unit) {
            $creditMemoUnit = $this->orderItemUnitCreditMemoUnitGenerator->generate($unit->id(), $unit->total());

            $taxTotal += $creditMemoUnit->getTaxTotal();
            $creditMemoUnits[] = $creditMemoUnit->serialize();
        }

        /** @var CustomRefund $custom */
        foreach ($customs as $custom) {
            $creditMemoUnit = $this->customCreditMemoUnitGenerator->generate($custom->id(), $custom->total(), [
                'productName' => $custom->label()
            ]);

            $taxTotal += $creditMemoUnit->getTaxTotal();
            $creditMemoUnits[] = $creditMemoUnit->serialize();
        }

        /** @var ShipmentRefund $shipment */
        foreach ($shipments as $shipment) {
            $creditMemoUnit = $this->shipmentCreditMemoUnitGenerator->generate($shipment->id(), $shipment->total());

            $taxTotal += $creditMemoUnit->getTaxTotal();
            $creditMemoUnits[] = $creditMemoUnit->serialize();
        }

        /** @var PaymentRefund $payment */
        foreach ($payments as $payment) {
            $creditMemoUnit = $this->paymentCreditMemoUnitGenerator->generate($payment->id(), $payment->total());

            $taxTotal += $creditMemoUnit->getTaxTotal();
            $creditMemoUnits[] = $creditMemoUnit->serialize();
        }

        /** @var FeeRefund $fee */
        foreach ($fees as $fee) {
            $creditMemoUnit = $this->feeCreditMemoUnitGenerator->generate($fee->id(), $fee->total(), $order->getItemUnits()->first());

            $taxTotal += $creditMemoUnit->getTaxTotal();
            $creditMemoUnits[] = $creditMemoUnit->serialize();
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

        $endOfMonth = (new \DateTime())
            ->modify('last day of this month')
            ->setTime(23, 59, 59);

        return new CreditMemo(
            $this->uuidCreditMemoIdentifierGenerator->generate(),
            $token,
            $this->creditMemoNumberGenerator->generate(),
            $orderNumber,
            $taxTotal,
            $total,
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            new CreditMemoChannel(
                $channel->getCode(),
                $channel->getName(),
                $channel->getColor()
            ),
            new CreditMemoPayment(
                $paymentMethod->getGatewayConfig()->getFactoryName(),
                $paymentMethod->getCode(),
                $paymentMethod->getName(),
                $paymentMethod->getInstructions(),
                ($invoice->paymentDueDate() === null || $invoice->paymentDueDate()->getTimestamp() < $endOfMonth->getTimestamp()) ? $endOfMonth : $invoice->paymentDueDate(),
                null,
                null
            ),
            $creditMemoUnits,
            $comment,
            $this->currentDateTimeProvider->now(),
            $billingData->serialize(),
            $shopBillingData->serialize()
        );
    }
}
