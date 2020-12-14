<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CreditMemoInterface extends ResourceInterface
{
    public function getToken(): string;

    public function getNumber(): string;

    public function getOrderNumber(): string;

    public function getTotal(): int;

    public function getCurrencyCode(): string;

    public function getLocaleCode(): string;

    public function getChannel(): CreditMemoChannel;

    public function getPayment(): CreditMemoPaymentInterface;

    public function getUnits(): array;

    public function getComment(): string;

    public function getIssuedAt(): \DateTimeInterface;
}
