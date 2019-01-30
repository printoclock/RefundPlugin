<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderRefundingCheckerInterface
{
    public function isOrderPartiallyRefunded(OrderInterface $order): bool;
    public function isOrderRefunded(OrderInterface $order): bool;
}
