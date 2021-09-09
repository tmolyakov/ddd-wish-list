<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\Exception\InvalidEmailException;

class Email extends AbstractValueObject
{
    /**
     * @var string
     */
    protected string $email;

    /**
     * @param string $email
     * @throws InvalidEmailException
     */
    public function __construct(string $email)
    {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
        $this->email = $email;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}