<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemo implements CreditMemoInterface
{
    /** @var string */
    private $id;

    /** @var string */
    private $token;

    /** @var string */
    private $number;

    /** @var string */
    private $orderNumber;

    /** @var int */
    private $taxTotal;

    /** @var int */
    private $total;

    /** @var string */
    private $currencyCode;

    /** @var string */
    private $localeCode;

    /** @var CreditMemoChannel */
    private $channel;

    /** @var array */
    private $units;

    /** @var string */
    private $comment;

    /** @var \DateTimeInterface */
    private $issuedAt;

    /** @var string */
    private $billingData;

    /** @var string */
    private $shopBillingData;

    /** @var string */
    private $paymentMethod;

    public function __construct(
        string $id,
        string $token,
        string $number,
        string $orderNumber,
        int $taxTotal,
        int $total,
        string $currencyCode,
        string $localeCode,
        CreditMemoChannel $channel,
        array $units,
        string $comment,
        \DateTimeInterface $issuedAt,
        string $billingData,
        string $shopBillingData,
        string $paymentMethod
    ) {
        $this->id = $id;
        $this->token = $token;
        $this->number = $number;
        $this->orderNumber = $orderNumber;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
        $this->currencyCode = $currencyCode;
        $this->localeCode = $localeCode;
        $this->channel = $channel;
        $this->units = $units;
        $this->comment = $comment;
        $this->issuedAt = $issuedAt;
        $this->billingData = $billingData;
        $this->shopBillingData = $shopBillingData;
        $this->paymentMethod = $paymentMethod;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getTaxTotal(): int
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(int $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getChannel(): CreditMemoChannel
    {
        return $this->channel;
    }

    public function getUnits(): array
    {
        $units = [];
        foreach ($this->units as $unit) {
            $units[] = CreditMemoUnit::unserialize($unit);
        }

        return $units;
    }

    public function getTaxItemsByTaxRate(float $defaultTaxRate = 0.2): array
    {
        $taxItems = [];

        /** @var CreditMemoUnit $unit */
        foreach ($this->getUnits() as $unit) {
            if ($unit->getTaxTotal() === 0) continue;

            $taxRate = sprintf('%.3f', round($unit->getTaxRate() ?? $defaultTaxRate, 3));

            if (!isset($taxItems[$taxRate])) {
                $taxItems[$taxRate] = 0;
            }

            $taxItems[$taxRate] += $unit->getTaxTotal();
        }

        if (empty($taxItems)) {
            $taxItems['default'] = 0;
        }

        ksort($taxItems);

        return $taxItems;
    }

    public function getTaxItemsByAccountingNumber(string $defaultAccountingNumber = ''): array
    {
        $taxItems = [];

        /** @var CreditMemoUnit $unit */
        foreach ($this->getUnits() as $unit) {
            if ($unit->getTaxTotal() === 0) continue;

            $accountingNumber = $unit->getTaxAccountingNumber() ?? $defaultAccountingNumber;

            if (!isset($taxItems[$accountingNumber])) {
                $taxItems[$accountingNumber] = 0;
            }

            $taxItems[$accountingNumber] += $unit->getTaxTotal();
        }

        if (empty($taxItems)) {
            $taxItems[$defaultAccountingNumber] = 0;
        }

        return $taxItems;
    }

    public function getLineItemsByAccountingNumber(string $defaultAccountingNumber = ''): array
    {
        $lineItems = [];

        /** @var CreditMemoUnit $unit */
        foreach ($this->getUnits() as $unit) {
            $accountingNumber = $unit->getServicesAccountingNumber() ?? $defaultAccountingNumber;

            if (!isset($lineItems[$accountingNumber])) {
                $lineItems[$accountingNumber] = 0;
            }

            $lineItems[$accountingNumber] += $unit->getSubtotal();
        }

        if (empty($lineItems)) {
            $lineItems[$defaultAccountingNumber] = 0;
        }

        return $lineItems;
    }

    public function setUnits(array $units): void {
        $this->units = $units;
    }

    public function getUnitsSubtotal(): int {
        return array_reduce(array_map(function (CreditMemoUnit $unit) {
            return $unit->getSubtotal();
        }, $this->getUnits()), function ($a, $b) {
            return $a + $b;
        }, 0);
    }

    public function getUnitsTaxTotal(): int {
        return array_reduce(array_map(function (CreditMemoUnit $unit) {
            return $unit->getTaxTotal();
        }, $this->getUnits()), function ($a, $b) {
            return $a + $b;
        }, 0);
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getIssuedAt(): \DateTimeInterface
    {
        return $this->issuedAt;
    }

    public function getBillingData(): CreditMemoBillingData
    {
        return CreditMemoBillingData::unserialize($this->billingData);
    }

    public function getShopBillingData(): CreditMemoBillingData
    {
        return CreditMemoBillingData::unserialize($this->shopBillingData);
    }

    public function getPaymentMethod(): CreditMemoPaymentMethod
    {
        return CreditMemoPaymentMethod::unserialize($this->paymentMethod);
    }
}
