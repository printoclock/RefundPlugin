<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

interface OrderPaymentStateResolverInterface
{
    public function resolve(string $orderNumber): void;
}
