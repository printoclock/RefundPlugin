<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Webmozart\Assert\Assert;

final class FeeCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    public function __construct(RepositoryInterface $orderItemUnitRepository)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
    }

    public function generate(int $unitId, int $amount = null, $extra = null): CreditMemoUnitInterface
    {
        if (empty($amount)) {
            return new CreditMemoUnit(
                RefundType::FEE,
                strval($unitId),
                null,
                [],
                null,
                0,
                null,
                0,
                0
            );
        }

        Assert::notNull($extra);

        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $this->orderItemUnitRepository->find($extra);
        Assert::notNull($orderItemUnit);

        $taxRate = ($orderItemUnit->getTaxRate() !== null) ? $orderItemUnit->getTaxRate() : ($orderItemUnit->getTaxTotal() / (float) ($orderItemUnit->getTotal() - $orderItemUnit->getTaxTotal()));
        $taxTotal = (int) (($amount / (1 + $taxRate)) * $taxRate);

        return new CreditMemoUnit(
            RefundType::FEE,
            strval($unitId),
            null,
            [],
            null,
            $amount - $taxTotal,
            $taxRate,
            $taxTotal,
            $amount
        );
    }
}
