<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\RefundPlugin\Entity\CreditMemoInterface;

interface CreditMemoGeneratorInterface
{
    public function generate(
        string $token,
        string $orderNumber,
        int $total,
        array $units,
        array $shipments,
        array $payments,
        array $fees,
        string $comment,
        int $paymentMethodId
    ): CreditMemoInterface;
}
