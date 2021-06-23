<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class CustomRefund implements UnitRefundInterface
{
    /** @var int */
    private $customId;

    /** @var int */
    private $total;

    public function __construct(int $customId, int $total)
    {
        $this->customId = $customId;
        $this->total = $total;
    }

    public function id(): int
    {
        return $this->customId;
    }

    public function total(): int
    {
        return $this->total;
    }
}
