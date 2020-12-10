<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Sylius\RefundPlugin\Model\FeeRefund;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\PaymentRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class GenerateCreditMemo
{
    /** @var string */
    private $token;

    /** @var string */
    private $orderNumber;

    /** @var int */
    private $total;

    /** @var array|OrderItemUnitRefund[] */
    private $units;

    /** @var array|ShipmentRefund[] */
    private $shipments;

    /** @var array|PaymentRefund[] */
    private $payments;

    /** @var array|FeeRefund[] */
    private $fees;

    /** @var string */
    private $comment;

    /** @var int */
    private $paymentMethodId;

    /** @var int */
    protected $feeAmount = 0;

    public function __construct(
        string $token,
        string $orderNumber,
        int $total,
        array $units,
        array $shipments,
        array $payments,
        array $fees,
        string $comment,
        int $paymentMethodId,
        int $feeAmount = 0
    ) {
        $this->token = $token;
        $this->orderNumber = $orderNumber;
        $this->total = $total;
        $this->units = $units;
        $this->shipments = $shipments;
        $this->payments = $payments;
        $this->fees = $fees;
        $this->comment = $comment;
        $this->paymentMethodId = $paymentMethodId;
        $this->feeAmount = $feeAmount;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function total(): int
    {
        return $this->total;
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

    /** @return array|PaymentRefund[] */
    public function payments(): array
    {
        return $this->payments;
    }

    /** @return array|FeeRefund[] */
    public function fees(): array
    {
        return $this->fees;
    }

    public function comment(): string
    {
        return $this->comment;
    }

    public function paymentMethodId(): int
    {
        return $this->paymentMethodId;
    }

    public function getFeeAmount(): int
    {
        return $this->feeAmount;
    }

    public function setFeeAmount(int $feeAmount): self
    {
        $this->feeAmount = $feeAmount;

        return $this;
    }
}
