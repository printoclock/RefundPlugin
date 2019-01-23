<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class FeeRefund implements UnitRefundInterface
{
    /** @var int */
    private $total;

    public function __construct(int $total)
    {
        $this->total = $total;
    }

    public function id(): ?int
    {
        return null;
    }

    public function total(): int
    {
        return $this->total;
    }
}
