<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface CreditMemoUnitInterface
{
    public function getType(): string;

    public function getProductName(): string;

    public function getVariantCode(): ?string;

    public function getVariantOptions(): array;

    public function getItemNumber(): ?string;

    public function getSubtotal(): int;

    public function getTaxTotal(): int;

    public function getTotal(): int;

    public function serialize(): string;
}
