<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface CreditMemoBillingDataInterface
{
    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getCompany(): ?string;

    public function getTaxId(): ?string;

    public function getStreet(): ?string;

    public function getPostcode(): ?string;

    public function getCity(): ?string;

    public function getCountryCode(): ?string;

    public function serialize(): string;
}
