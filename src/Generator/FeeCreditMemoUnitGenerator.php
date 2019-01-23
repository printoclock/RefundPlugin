<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;
use Webmozart\Assert\Assert;

final class FeeCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    public function __construct(RepositoryInterface $orderItemUnitRepository)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
    }

    public function generate(int $unitId, int $amount = null): CreditMemoUnitInterface
    {
        if (empty($amount)) {
            return new CreditMemoUnit(
                'Frais de gestion',
                0,
                0
            );
        }

        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $this->orderItemUnitRepository->find($unitId);
        Assert::notNull($orderItemUnit);

        $taxRate = $orderItemUnit->getTaxTotal() / (float) ($orderItemUnit->getTotal() - $orderItemUnit->getTaxTotal());

        return new CreditMemoUnit(
            'Frais de gestion',
            $amount,
            (int) (($amount / (1 + $taxRate)) * $taxRate)
        );
    }
}
