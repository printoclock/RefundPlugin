<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Sylius\RefundPlugin\Model\FeeRefund;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\PaymentRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class RefundUnits
{
    /** @var string */
    private $orderNumber;

    /** @var array|OrderItemUnitRefund[] */
    private $units;

    /** @var array|ShipmentRefund[] */
    private $shipments;

    /** @var array|PaymentRefund[] */
    private $payments;

    /** @var array|FeeRefund[] */
    private $fees;

    /** @var int */
    private $paymentMethodId;

    /** @var \DateTime|null */
    private $payedAt;

    /** @var string|null */
    private $reference;

    /** @var string|null */
    private $comment;

    public function __construct(
        string $orderNumber,
        array $units,
        array $shipments,
        array $payments,
        array $fees,
        int $paymentMethodId,
        ?\DateTime $payedAt = null,
        ?string $reference = null,
        ?string $comment = null
    ) {
        $this->orderNumber = $orderNumber;
        $this->units = $units;
        $this->shipments = $shipments;
        $this->payments = $payments;
        $this->fees = $fees;
        $this->paymentMethodId = $paymentMethodId;
        $this->payedAt = $payedAt;
        $this->reference = $reference;
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

    public function paymentMethodId(): int
    {
        return $this->paymentMethodId;
    }

    public function payedAt(): ?\DateTime
    {
        return $this->payedAt;
    }

    public function reference(): ?string
    {
        return $this->reference;
    }

    public function comment(): ?string
    {
        return $this->comment;
    }
}
