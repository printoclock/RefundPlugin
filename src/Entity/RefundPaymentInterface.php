<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface RefundPaymentInterface extends ResourceInterface
{
    public const STATE_NEW = 'new';
    public const STATE_COMPLETED = 'completed';

    public function getToken(): string;

    public function getOrderNumber(): string;

    public function getAmount(): int;

    public function getFeeAmount(): int;

    public function getCurrencyCode(): string;

    public function getState(): string;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function getPaymentMethod(): PaymentMethodInterface;

    public function getPayedAt(): ?\DateTime;

    public function getReference(): ?string;

    public function getComment(): ?string;
}
