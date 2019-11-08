<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemoUnit implements CreditMemoUnitInterface
{
    /** @var string */
    private $type;

    /** @var string */
    private $productName;

    /** @var string|null */
    private $variantCode;

    /** @var array|null */
    private $variantOptions;

    /** @var string|null */
    private $itemNumber;

    /** @var int */
    private $subtotal;

    /** @var float|null */
    protected $taxRate;

    /** @var int */
    private $taxTotal;

    /** @var int */
    private $total;

    public function __construct(string $type, string $productName, ?string $variantCode, array $variantOptions, ?string $itemNumber, int $subtotal, ?float $taxRate, int $taxTotal, int $total)
    {
        $this->type = $type;
        $this->productName = $productName;
        $this->variantCode = $variantCode;
        $this->variantOptions = $variantOptions;
        $this->itemNumber = $itemNumber;
        $this->subtotal = $subtotal;
        $this->taxRate = $taxRate;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getVariantCode(): ?string
    {
        return $this->variantCode;
    }

    public function getVariantOptions(): array
    {
        return $this->variantOptions ?? [];
    }

    public function getItemNumber(): ?string
    {
        return $this->itemNumber;
    }

    public function getSubtotal(): int
    {
        return $this->subtotal;
    }

    public function setSubtotal(int $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): void
    {
        $this->taxRate = $taxRate;
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

    public function serialize(): string
    {
        $serialized = json_encode([
            'type' => $this->type,
            'product_name' => $this->productName,
            'variant_code' => $this->variantCode,
            'variant_options' => $this->variantOptions,
            'item_number' => $this->itemNumber,
            'subtotal' => $this->subtotal,
            'tax_rate' => $this->taxRate,
            'tax_total' => $this->taxTotal,
            'total' => $this->total,
        ]);

        if ($serialized === false) {
            throw new \Exception('Credit memo unit could have not been serialized');
        }

        return $serialized;
    }

    public static function unserialize(string $serialized): self
    {
        $data = json_decode($serialized, true);

        return new self(
            $data['type'],
            $data['product_name'],
            $data['variant_code'],
            $data['variant_options'],
            $data['item_number'],
            $data['subtotal'],
            $data['tax_rate'],
            $data['tax_total'],
            $data['total']
        );
    }
}
