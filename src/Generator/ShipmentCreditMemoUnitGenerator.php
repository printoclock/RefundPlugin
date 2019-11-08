<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\AdjustmentInterface as OrderAdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Webmozart\Assert\Assert;

final class ShipmentCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    public function __construct(RepositoryInterface $adjustmentRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
    }

    public function generate(int $unitId, int $amount = null, $extra = null): CreditMemoUnitInterface
    {
        /** @var OrderAdjustmentInterface $shippingAdjustment */
        $shippingAdjustment = $this->adjustmentRepository->findOneBy(['id' => $unitId, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT]);
        Assert::notNull($shippingAdjustment);

        /** @var OrderInterface $order */
        $order = $shippingAdjustment->getAdjustable();

        $shippingSubtotal = $order->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingPromotionTotal = $order->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingTaxTotal = $order->getAdjustmentsTotal(AdjustmentInterface::TAX_ADJUSTMENT);
        $shippingTotal = $shippingSubtotal + $shippingPromotionTotal + $shippingTaxTotal;

        Assert::lessThanEq($amount, $shippingTotal);

        $shippingTaxTotal = ($amount === $shippingTotal) ? $shippingTaxTotal : (int) ($shippingTaxTotal * ($amount / $shippingTotal));

        return new CreditMemoUnit(
            RefundType::SHIPMENT,
            $shippingAdjustment->getLabel(),
            null,
            [],
            null,
            $amount - $shippingTaxTotal,
            null,
            $shippingTaxTotal,
            $amount
        );
    }
}
