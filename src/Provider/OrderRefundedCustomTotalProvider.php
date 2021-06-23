<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;

final class OrderRefundedCustomTotalProvider implements OrderRefundedTotalProviderInterface
{
    /** @var RepositoryInterface */
    private $refundRepository;

    public function __construct(RepositoryInterface $refundRepository)
    {
        $this->refundRepository = $refundRepository;
    }

    public function __invoke(string $orderNumber): int
    {
        $refunds = $this->refundRepository->findBy([
            'orderNumber' => $orderNumber,
            'type' => RefundType::CUSTOM,
        ]);

        $orderRefundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            $orderRefundedTotal += $refund->getAmount();
        }

        return $orderRefundedTotal;
    }
}
