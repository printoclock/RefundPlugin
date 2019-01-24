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

        $shippingPromotionTotal = $order->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingTaxTotal = $order->getAdjustmentsTotal(AdjustmentInterface::TAX_ADJUSTMENT);
        $shippingTotal = $shippingAdjustment->getAmount() + $shippingPromotionTotal + $shippingTaxTotal;

        Assert::lessThanEq($amount, $shippingTotal);

        if ($amount === $shippingTotal) {
            return new CreditMemoUnit(
                RefundType::SHIPMENT,
                $shippingAdjustment->getLabel(),
                $shippingTotal,
                $shippingTaxTotal
            );
        }

        return new CreditMemoUnit(
            RefundType::SHIPMENT,
            $shippingAdjustment->getLabel(),
            $amount,
            (int) ($shippingTaxTotal * ($amount / $shippingTotal))
        );
    }
}
