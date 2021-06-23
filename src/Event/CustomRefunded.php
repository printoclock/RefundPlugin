<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class CustomRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var int */
    private $customUnitId;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, int $customUnitId, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->customUnitId = $customUnitId;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function customUnitId(): int
    {
        return $this->customUnitId;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
