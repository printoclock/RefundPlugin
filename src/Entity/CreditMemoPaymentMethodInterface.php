<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface CreditMemoPaymentMethodInterface
{
    public function getCode(): ?string;

    public function getName(): ?string;

    public function getInstructions(): ?string;

    public function getAccountingCode(): ?string;

    public function getAccountingNumber(): ?string;

    public function serialize(): string;
}
