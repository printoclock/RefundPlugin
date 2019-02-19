<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class PaymentRefund implements UnitRefundInterface
{
    /** @var int */
    private $paymentId;

    /** @var int */
    private $total;

    public function __construct(int $paymentId, int $total)
    {
        $this->paymentId = $paymentId;
        $this->total = $total;
    }

    public function id(): int
    {
        return $this->paymentId;
    }

    public function total(): int
    {
        return $this->total;
    }
}
