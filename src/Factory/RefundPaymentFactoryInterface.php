<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RefundPaymentFactoryInterface
{
    public function createWithData(
        string $token,
        string $orderNumber,
        int $amount,
        int $feeAmount,
        string $currencyCode,
        string $state,
        int $paymentMethodId,
        ?\DateTime $payedAt = null,
        ?string $reference = null,
        ?string $comment = null
    ): RefundPaymentInterface;
}
