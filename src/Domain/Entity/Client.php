<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\PersonName;
use App\Domain\ValueObject\RussianPhone;

class Client
{
    /**
     * @var ID
     */
    protected $id;

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

    /**
     * @return ID
     */
    public function getId(): ID
    {
        return $this->id;
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
    public function setName(PersonName $name): self
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
    public function setEmail(Email $email): self
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
    public function setPhone(RussianPhone $phone): self
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
    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }
}