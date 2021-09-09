<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class PersonName extends AbstractValueObject
{
    /**
     * @var string
     */
    protected string $firstName;

    /**
     * @var string
     */
    protected string $lastName;

    /**
     * @var string
     */
    protected string $middleName;

    /**
     * @var string
     */
    protected string $fullName;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->fullName = $name;
        $this->normalizeName($name);
    }

    /**
     * @param string $name
     * @return void
     */
    protected function normalizeName(string $name): void
    {
        [$this->lastName, $this->firstName, $this->middleName] = explode(' ', $name);
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->getFullName();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFullName();
    }
}