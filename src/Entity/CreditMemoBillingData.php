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

    public function __construct(
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $company = null,
        ?string $taxId = null,
        ?string $street = null,
        ?string $postcode = null,
        ?string $city = null,
        ?string $countryCode = null
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
            $data['first_name'],
            $data['last_name'],
            $data['company'],
            $data['tax_id'],
            $data['street'],
            $data['postcode'],
            $data['city'],
            $data['country_code']
        );
    }
}
