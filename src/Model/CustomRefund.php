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

    /** @var array */
    private $taxRate;

    public function __construct(int $customId, int $total, string $label, array $taxRate = [])
    {
        $this->customId = $customId;
        $this->total = $total;
        $this->label = $label;
        $this->taxRate = $taxRate;
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

    public function taxRate(): array
    {
        return $this->taxRate;
    }
}
