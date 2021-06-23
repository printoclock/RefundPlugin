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

        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $order->getItemUnits()->first();

        $taxRate = ($orderItemUnit !== null && $orderItemUnit->getTaxRate() !== null) ? $orderItemUnit->getTaxRate() : 0.2;
        $taxTotal = (int) (($amount / (1 + $taxRate)) * $taxRate);

        return new CreditMemoUnit(
            RefundType::CUSTOM,
            ($extra !== null && isset($extra['productName'])) ? $extra['productName'] : '',
            null,
            [],
            null,
            $amount - $taxTotal,
            $taxRate,
            $taxTotal,
            $amount,
            ($orderItemUnit !== null) ? $orderItemUnit->getServicesAccountingNumber() : null,
            ($orderItemUnit !== null) ? $orderItemUnit->getTaxAccountingNumber() : null
        );
    }
}
