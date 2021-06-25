<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Webmozart\Assert\Assert;

final class CustomCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $orderRepository;

    public function __construct(RepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function generate(int $unitId, int $amount = null, $extra = null): CreditMemoUnitInterface
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($unitId);
        Assert::notNull($order);
        Assert::lessThanEq($amount, $order->getTotal());

        if (isset($extra['taxRate']) && !empty($taxRate = $extra['taxRate'])) {
            $taxRateAmount = (float) $taxRate['amount'];
            $servicesAccountingNumber = $taxRate['servicesAccountingNumber'];
            $taxAccountingNumber = $taxRate['taxAccountingNumber'];
        } else {
            /** @var OrderItemUnitInterface|null $orderItemUnit */
            $orderItemUnit = (false !== $orderItemUnit = $order->getItemUnits()->first()) ? $orderItemUnit : null;

            $taxRateAmount = ($orderItemUnit !== null && $orderItemUnit->getTaxRate() !== null) ? $orderItemUnit->getTaxRate() : 0.2;
            $servicesAccountingNumber = ($orderItemUnit !== null) ? $orderItemUnit->getServicesAccountingNumber() : null;
            $taxAccountingNumber = ($orderItemUnit !== null) ? $orderItemUnit->getTaxAccountingNumber() : null;
        }

        $taxTotal = (int) (($amount / (1 + $taxRateAmount)) * $taxRateAmount);

        return new CreditMemoUnit(
            RefundType::CUSTOM,
            ($extra !== null && isset($extra['productName'])) ? $extra['productName'] : '',
            null,
            [],
            null,
            $amount - $taxTotal,
            $taxRateAmount,
            $taxTotal,
            $amount,
            $servicesAccountingNumber,
            $taxAccountingNumber
        );
    }
}
