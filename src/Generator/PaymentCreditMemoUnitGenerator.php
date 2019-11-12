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

final class PaymentCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    public function __construct(RepositoryInterface $adjustmentRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
    }

    public function generate(int $unitId, int $amount = null, $extra = null): CreditMemoUnitInterface
    {
        return new CreditMemoUnit(
            RefundType::PAYMENT,
            '',
            null,
            [],
            null,
            $amount,
            null,
            0,
            $amount,
            null
        );
    }
}
