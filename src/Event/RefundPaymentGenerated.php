<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class RefundPaymentGenerated
{
    /** @var int */
    private $id;

    /** @var string */
    private $orderNumber;

    /** @var int */
    private $amount;

    /** @var int */
    private $feeAmount;

    /** @var string */
    private $currencyCode;

    /** @var int */
    private $paymentMethodId;

    /** @var int */
    private $paymentId;

    public function __construct(
        int $id,
        string $orderNumber,
        int $amount,
        int $feeAmount,
        string $currencyCode,
        int $paymentMethodId,
        int $paymentId
    ) {
        $this->id = $id;
        $this->orderNumber = $orderNumber;
        $this->amount = $amount;
        $this->feeAmount = $feeAmount;
        $this->currencyCode = $currencyCode;
        $this->paymentMethodId = $paymentMethodId;
        $this->paymentId = $paymentId;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function feeAmount(): int
    {
        return $this->feeAmount;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function paymentMethodId(): int
    {
        return $this->paymentMethodId;
    }

    public function paymentId(): int
    {
        return $this->paymentId;
    }
}
