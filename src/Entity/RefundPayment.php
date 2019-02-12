<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Core\Model\PaymentMethodInterface;

/** @final */
class RefundPayment implements RefundPaymentInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $token;

    /** @var string */
    private $orderNumber;

    /** @var int */
    private $amount;

    /** @var int */
    private $feeAmount;

    /** @var string */
    private $currencyCode;

    /** @var string */
    private $state;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    /** @var PaymentMethodInterface */
    private $paymentMethod;

    /** @var string|null */
    private $reference;

    /** @var string|null */
    private $comment;

    public function __construct(
        string $token,
        string $orderNumber,
        int $amount,
        int $feeAmount,
        string $currencyCode,
        string $state,
        PaymentMethodInterface $paymentMethod,
        ?string $reference = null,
        ?string $comment = null
    ) {
        $this->token = $token;
        $this->orderNumber = $orderNumber;
        $this->amount = $amount;
        $this->feeAmount = $feeAmount;
        $this->currencyCode = $currencyCode;
        $this->state = $state;
        $this->paymentMethod = $paymentMethod;
        $this->reference = $reference;
        $this->comment = $comment;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getFeeAmount(): int
    {
        return $this->feeAmount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPaymentMethod(): PaymentMethodInterface
    {
        return $this->paymentMethod;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
