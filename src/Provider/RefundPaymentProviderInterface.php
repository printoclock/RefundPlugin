<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

interface RefundPaymentProviderInterface
{
    public function __invoke(string $orderNumber, bool $excludeFees = true): int;
}
