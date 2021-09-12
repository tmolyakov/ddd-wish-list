<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Entity\Client as DomainClient;
use App\Domain\Factory\ClientFactoryInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\PersonName;
use App\Domain\ValueObject\RussianPhone;
use App\Infrastructure\Entity\Client;

class ClientFactory implements ClientFactoryInterface
{
    /**
     * @var PersonName
     */
    protected $name;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var RussianPhone
     */
    protected $phone;

    /**
     * @var string
     */
    protected $address;

    protected function __construct()
    {
    }

    /**
     * @return static
     */
    public static function create(): ClientFactoryInterface
    {
        return new self();
    }

    /**
     * {@inheritDoc}
     */
    public function make(): DomainClient
    {
        return (new Client())
            ->setName($this->getName())
            ->setEmail($this->getEmail())
            ->setPhone($this->getPhone())
            ->setAddress($this->getAddress());
    }

    /**
     * @return PersonName
     */
    public function getName(): PersonName
    {
        return $this->name;
    }

    /**
     * @param PersonName $name
     * @return $this
     */
    public function setName(PersonName $name): ClientFactoryInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @param Email $email
     * @return $this
     */
    public function setEmail(Email $email): ClientFactoryInterface
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return RussianPhone
     */
    public function getPhone(): RussianPhone
    {
        return $this->phone;
    }

    /**
     * @param RussianPhone $phone
     * @return $this
     */
    public function setPhone(RussianPhone $phone): ClientFactoryInterface
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address): ClientFactoryInterface
    {
        $this->address = $address;

        return $this;
    }
}
