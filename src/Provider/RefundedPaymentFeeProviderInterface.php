<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

interface RefundedPaymentFeeProviderInterface
{
    public function getFeeOfPayment(int $adjustmentId): int;
}
