<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page;

use Sylius\Behat\Page\SymfonyPageInterface;

interface OrderRefundsPageInterface extends SymfonyPageInterface
{
    public function countRefundableUnitsWithProduct(string $productName): int;

    public function getRefundedTotal(): string;

    public function pickUnitWithProductToRefund(string $productName, int $unitNumber): void;

    public function pickAllUnitsToRefund(): void;

    public function refund(): void;

    public function isUnitWithProductAvailableToRefund(string $productName, int $unitNumber): bool;

    public function hasBackButton(): bool;
}