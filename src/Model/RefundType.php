<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

use Sylius\RefundPlugin\Exception\RefundTypeNotResolved;

final class RefundType
{
    public const ORDER_ITEM_UNIT = 'order_item_unit';
    public const SHIPMENT = 'shipment';
    public const FEE = 'fee';

    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::ORDER_ITEM_UNIT, self::SHIPMENT, self::FEE])) {
            throw RefundTypeNotResolved::withType($value);
        }

        $this->value = $value;
    }

    public static function orderItemUnit(): self
    {
        return new self(self::ORDER_ITEM_UNIT);
    }

    public static function shipment(): self
    {
        return new self(self::SHIPMENT);
    }

    public static function fee(): self
    {
        return new self(self::FEE);
    }

    public function equals(self $refundType): bool
    {
        return $this->__toString() === $refundType->__toString();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
