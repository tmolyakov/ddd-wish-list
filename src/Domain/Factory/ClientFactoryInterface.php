<?php

namespace App\Domain\Factory;

use App\Domain\Entity\Client;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\PersonName;
use App\Domain\ValueObject\RussianPhone;

interface ClientFactoryInterface
{
    public function make(): Client;

    /**
     * @param PersonName $name
     * @return $this
     */
    public function setName(PersonName $name): ClientFactoryInterface;

    /**
     * @param Email $email
     * @return $this
     */
    public function setEmail(Email $email): ClientFactoryInterface;

    /**
     * @param RussianPhone $phone
     * @return $this
     */
    public function setPhone(RussianPhone $phone): ClientFactoryInterface;

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address): ClientFactoryInterface;
}
