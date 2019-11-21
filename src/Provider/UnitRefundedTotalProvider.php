<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;

final class UnitRefundedTotalProvider implements UnitRefundedTotalProviderInterface
{
    /** @var RepositoryInterface */
    private $refundRepository;

    public function __construct(RepositoryInterface $refundRepository)
    {
        $this->refundRepository = $refundRepository;
    }

    public function __invoke(int $unitId, RefundType $type, ?string $orderNumber = null): int
    {
        $params = ['refundedUnitId' => $unitId, 'type' => $type->__toString()];
        if (!empty($orderNumber)) {
            $params['orderNumber'] = $orderNumber;
        }

        $refunds = $this->refundRepository->findBy($params);

        $refundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            $refundedTotal += $refund->getAmount();
        }

        return $refundedTotal;
    }
}
