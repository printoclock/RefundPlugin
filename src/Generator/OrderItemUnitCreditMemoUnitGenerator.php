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

final class OrderItemUnitCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    public function __construct(RepositoryInterface $orderItemUnitRepository)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
    }

    public function generate(int $unitId, int $amount = null, $extra = null): CreditMemoUnitInterface
    {
        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $this->orderItemUnitRepository->find($unitId);
        Assert::notNull($orderItemUnit);
        Assert::lessThanEq($amount, $orderItemUnit->getTotal());

        /** @var OrderItemInterface $orderItem */
        $orderItem = $orderItemUnit->getOrderItem();

        $variant = $orderItem->getVariant();

        $taxTotal = ($amount === $orderItemUnit->getTotal()) ? $orderItemUnit->getTaxTotal() : (int) ($orderItemUnit->getTaxTotal() * ($amount / $orderItemUnit->getTotal()));

        return new CreditMemoUnit(
            RefundType::ORDER_ITEM_UNIT,
            $orderItem->getProductName(),
            ($variant !== null) ? $variant->getCode() : null,
            ($variant !== null) ? $variant->getOptionsDisplayAsArray() : [],
            $orderItem->getNumber(),
            $amount - $taxTotal,
            $taxTotal,
            $amount
        );
    }
}
