<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

final class OrderPaymentTransitions
{
    public const GRAPH = 'sylius_order_payment';

    public const TRANSITION_PARTIALLY_REFUND = 'partially_refund';
    public const TRANSITION_REFUND = 'refund';

    public const STATE_PARTIALLY_REFUNDED = 'partially_refunded';
    public const STATE_REFUNDED = 'refunded';

    private function __construct()
    {
    }
}
