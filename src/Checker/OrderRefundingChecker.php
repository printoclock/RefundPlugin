<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderRefundingChecker implements OrderRefundingCheckerInterface
{
    /** @var OrderRefundedTotalProviderInterface */
    private $orderRefundedTotalProvider;

    public function __construct(OrderRefundedTotalProviderInterface $orderRefundedTotalProvider)
    {
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
    }

    public function isOrderPartiallyRefunded(OrderInterface $order): bool {
        return false;
    }

    public function isOrderRefunded(OrderInterface $order): bool {
        return false;
    }

    public function isOrderFullyRefunded(OrderInterface $order): bool
    {
        return $order->getTotal() === $this->orderRefundedTotalProvider->__invoke($order->getNumber());
    }
}
