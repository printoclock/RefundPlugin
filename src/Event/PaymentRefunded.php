<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class PaymentRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var int */
    private $paymentUnitId;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, int $paymentUnitId, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->paymentUnitId = $paymentUnitId;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function paymentUnitId(): int
    {
        return $this->paymentUnitId;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
