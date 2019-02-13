<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemoPaymentMethod implements CreditMemoPaymentMethodInterface
{
    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $instructions;

    /** @var string|null */
    protected $accountingCode;

    /** @var string|null */
    protected $accountingNumber;

    public function __construct(
        ?string $code,
        ?string $name,
        ?string $instructions,
        ?string $accountingCode,
        ?string $accountingNumber
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->instructions = $instructions;
        $this->accountingCode = $accountingCode;
        $this->accountingNumber = $accountingNumber;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }
    public function getAccountingCode(): ?string
    {
        return $this->accountingCode;
    }

    public function getAccountingNumber(): ?string
    {
        return $this->accountingNumber;
    }

    public function serialize(): string
    {
        $serialized = json_encode([
            'code' => $this->code,
            'name' => $this->name,
            'instructions' => $this->instructions,
            'accounting_code' => $this->accountingCode,
            'accounting_number' => $this->accountingNumber
        ]);

        if ($serialized === false) {
            throw new \Exception('Credit memo payment method could have not been serialized');
        }

        return $serialized;
    }

    public static function unserialize(string $serialized): self
    {
        $data = json_decode($serialized, true);

        return new self(
            $data['code'],
            $data['name'],
            $data['instructions'],
            $data['accounting_code'],
            $data['accounting_number']
        );
    }
}
