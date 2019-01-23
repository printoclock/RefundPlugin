<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class FeeRefund implements UnitRefundInterface
{
    /** @var int */
    private $feeId;

    /** @var int */
    private $total;

    public function __construct(int $feeId, int $total)
    {
        $this->feeId = $feeId;
        $this->total = $total;
    }

    public function id(): int
    {
        return $this->feeId;
    }

    public function total(): int
    {
        return $this->total;
    }
}
