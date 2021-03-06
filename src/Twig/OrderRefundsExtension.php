<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Twig;

use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;
use Sylius\RefundPlugin\Provider\UnitRefundedTotalProviderInterface;

final class OrderRefundsExtension extends \Twig_Extension
{
    /** @var OrderRefundedTotalProviderInterface */
    private $orderRefundedSubtotalTotalProvider;

    /** @var OrderRefundedTotalProviderInterface */
    private $orderRefundedFeeTotalProvider;

    /** @var OrderRefundedTotalProviderInterface */
    private $orderRefundedTotalProvider;

    /** @var UnitRefundedTotalProviderInterface */
    private $unitRefundedTotalProvider;

    /** @var UnitRefundingAvailabilityCheckerInterface */
    private $unitRefundingAvailabilityChecker;

    public function __construct(
        OrderRefundedTotalProviderInterface $orderRefundedSubtotalTotalProvider,
        OrderRefundedTotalProviderInterface $orderRefundedFeeTotalProvider,
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider,
        UnitRefundedTotalProviderInterface $unitRefundedTotalProvider,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker
    ) {
        $this->orderRefundedSubtotalTotalProvider = $orderRefundedSubtotalTotalProvider;
        $this->orderRefundedFeeTotalProvider = $orderRefundedFeeTotalProvider;
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
        $this->unitRefundedTotalProvider = $unitRefundedTotalProvider;
        $this->unitRefundingAvailabilityChecker = $unitRefundingAvailabilityChecker;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function(
                'order_refunded_subtotal',
                [$this->orderRefundedSubtotalTotalProvider, '__invoke']
            ),
            new \Twig_Function(
                'order_refunded_fee_total',
                [$this->orderRefundedFeeTotalProvider, '__invoke']
            ),
            new \Twig_Function(
                'order_refunded_total',
                [$this->orderRefundedTotalProvider, '__invoke']
            ),
            new \Twig_Function(
                'unit_refunded_total',
                [$this, 'getUnitRefundedTotal']
            ),
            new \Twig_Function(
                'can_unit_be_refunded',
                [$this, 'canUnitBeRefunded']
            ),
        ];
    }

    public function canUnitBeRefunded(int $unitId, string $refundType, ?string $orderNumber = null): bool
    {
        return $this->unitRefundingAvailabilityChecker->__invoke($unitId, new RefundType($refundType), $orderNumber);
    }

    public function getUnitRefundedTotal(int $unitId, string $refundType, ?string $orderNumber = null): int
    {
        return $this->unitRefundedTotalProvider->__invoke($unitId, new RefundType($refundType), $orderNumber);
    }
}
