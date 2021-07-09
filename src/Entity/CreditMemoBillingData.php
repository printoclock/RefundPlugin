<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemoBillingData implements CreditMemoBillingDataInterface
{
    /** @var string|null */
    private $firstName;

    /** @var string|null */
    private $lastName;

    /** @var string|null */
    private $company;

    /** @var string|null */
    private $taxId;

    /** @var string|null */
    private $street;

    /** @var string|null */
    private $postcode;

    /** @var string|null */
    private $city;

    /** @var string|null */
    private $countryCode;

    /** @var string|null */
    private $vatNumber;

    public function __construct(
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $company = null,
        ?string $taxId = null,
        ?string $street = null,
        ?string $postcode = null,
        ?string $city = null,
        ?string $countryCode = null,
        ?string $vatNumber = null
    )
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->company = $company;
        $this->taxId = $taxId;
        $this->street = $street;
        $this->postcode = $postcode;
        $this->city = $city;
        $this->countryCode = $countryCode;
        $this->vatNumber = $vatNumber;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function serialize(): string
    {
        $serialized = json_encode([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'company' => $this->company,
            'tax_id' => $this->taxId,
            'street' => $this->street,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'country_code' => $this->countryCode,
            'vat_number' => $this->vatNumber,
        ]);

        if ($serialized === false) {
            throw new \Exception('Credit memo billing data could have not been serialized');
        }

        return $serialized;
    }

    public static function unserialize(string $serialized): self
    {
        $data = json_decode($serialized, true);

        return new self(
            isset($data['first_name']) ? $data['first_name'] : null,
            isset($data['last_name']) ? $data['last_name'] : null,
            isset($data['company']) ? $data['company'] : null,
            isset($data['tax_id']) ? $data['tax_id'] : null,
            isset($data['street']) ? $data['street'] : null,
            isset($data['postcode']) ? $data['postcode'] : null,
            isset($data['city']) ? $data['city'] : null,
            isset($data['country_code']) ? $data['country_code'] : null,
            isset($data['vat_number']) ? $data['vat_number'] : null
        );
    }
}
