<?php

declare(strict_types=1);

namespace App\Infrastructure\Entity;

use App\Domain\Entity\Client as DomainClient;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\PersonName;
use App\Domain\ValueObject\RussianPhone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\ClientRepository")
 */
class Client extends DomainClient
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $address;

    public function getId(): ID
    {
        return new ID($this->id);
    }

    public function getName(): PersonName
    {
        return new PersonName($this->name);
    }

    /**
     * @param PersonName $name
     * @return $this
     */
    public function setName(PersonName $name): DomainClient
    {
        $this->name = (string) $name;

        return $this;
    }

    public function getEmail(): Email
    {
        return new Email($this->email);
    }

    /**
     * @param Email $email
     * @return $this
     */
    public function setEmail(Email $email): DomainClient
    {
        $this->email = (string) $email;

        return $this;
    }

    public function getPhone(): RussianPhone
    {
        return new RussianPhone($this->phone);
    }

    /**
     * @param RussianPhone $phone
     * @return $this
     */
    public function setPhone(RussianPhone $phone): DomainClient
    {
        $this->phone = (string) $phone;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address): DomainClient
    {
        $this->address = $address;

        return $this;
    }
}
