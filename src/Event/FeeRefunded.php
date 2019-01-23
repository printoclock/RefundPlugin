<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class FeeRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
