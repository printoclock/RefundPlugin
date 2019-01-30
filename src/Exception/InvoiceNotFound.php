<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class InvoiceNotFound extends \InvalidArgumentException
{
    public static function withNumber(string $orderNumber): self
    {
        return new self(sprintf('Invoice with order number "%s" has not been found', $orderNumber));
    }
}
