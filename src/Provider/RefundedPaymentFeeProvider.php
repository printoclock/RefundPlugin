<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class RefundedPaymentFeeProvider implements RefundedPaymentFeeProviderInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    public function __construct(RepositoryInterface $adjustmentRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
    }

    public function getFeeOfPayment(int $adjustmentId): int
    {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentRepository->find($adjustmentId);
        Assert::notNull($adjustment);

        return $adjustment->getAmount();
    }
}
