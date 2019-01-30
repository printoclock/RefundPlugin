<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

use Sylius\RefundPlugin\Model\FeeRefund;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class UnitsRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var array|OrderItemUnitRefund[] */
    private $units;

    /** @var array|ShipmentRefund[] */
    private $shipments;

    /** @var array|FeeRefund[] */
    private $fees;

    /** @var int */
    private $paymentMethodId;

    /** @var int */
    private $amount;

    /** @var int */
    private $feeAmount;

    /** @var string */
    private $currencyCode;

    /** @var string */
    private $comment;

    public function __construct(
        string $orderNumber,
        array $units,
        array $shipments,
        array $fees,
        int $paymentMethodId,
        int $amount,
        int $feeAmount,
        string $currencyCode,
        string $comment
    ) {
        $this->orderNumber = $orderNumber;
        $this->units = $units;
        $this->shipments = $shipments;
        $this->fees = $fees;
        $this->paymentMethodId = $paymentMethodId;
        $this->amount = $amount;
        $this->feeAmount = $feeAmount;
        $this->currencyCode = $currencyCode;
        $this->comment = $comment;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    /** @return array|OrderItemUnitRefund[] */
    public function units(): array
    {
        return $this->units;
    }

    /** @return array|ShipmentRefund[] */
    public function shipments(): array
    {
        return $this->shipments;
    }

    /** @return array|FeeRefund[] */
    public function fees(): array
    {
        return $this->fees;
    }

    public function paymentMethodId(): int
    {
        return $this->paymentMethodId;
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

    public function comment(): string
    {
        return $this->comment;
    }
}
