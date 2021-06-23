<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class CustomRefund implements UnitRefundInterface
{
    /** @var int */
    private $customId;

    /** @var int */
    private $total;

    /** @var string */
    private $label;

    public function __construct(int $customId, int $total, string $label)
    {
        $this->customId = $customId;
        $this->total = $total;
        $this->label = $label;
    }

    public function id(): int
    {
        return $this->customId;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function label(): string
    {
        return $this->label;
    }
}
