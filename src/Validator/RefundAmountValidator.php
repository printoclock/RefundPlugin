<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Exception\InvalidRefundAmountException;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundAmountValidator implements RefundAmountValidatorInterface
{
    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    public function __construct(RemainingTotalProviderInterface $unitRefundedTotalProvider)
    {
        $this->remainingTotalProvider = $unitRefundedTotalProvider;
    }

    public function validateUnits(array $unitRefunds, RefundType $refundType, ?string $orderNumber = null): void
    {
        /** @var UnitRefundInterface $unitRefund */
        foreach ($unitRefunds as $unitRefund) {
            if ($refundType->equals(RefundType::fee())) {
                if ($unitRefund->id() < 1000) continue;
            }

            if ($unitRefund->total() <= 0) {
                throw InvalidRefundAmountException::withValidationConstraint(RefundUnitsValidationConstraintMessages::REFUND_AMOUNT_MUST_BE_GREATER_THAN_ZERO);
            }

            $unitRefundedTotal = $this->remainingTotalProvider->getTotalLeftToRefund(
                ($refundType->equals(RefundType::fee())) ? $unitRefund->id() / 1000 : $unitRefund->id(),
                $refundType,
                $orderNumber
            );

            if ($unitRefund->total() > $unitRefundedTotal) {
                throw InvalidRefundAmountException::withValidationConstraint(RefundUnitsValidationConstraintMessages::REFUND_AMOUNT_MUST_BE_LESS_THAN_AVAILABLE);
            }
        }
    }
}
