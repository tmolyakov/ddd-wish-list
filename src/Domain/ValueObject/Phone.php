<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\Exception\InvalidPhoneException;

abstract class Phone extends AbstractValueObject
{
    /**
     * @var string
     */
    protected string $phone;

    /**
     * @param string $phone
     */
    public function __construct(string $phone)
    {
        $this->validate($phone);

        $this->phone = $this->normalizeNumber($phone);
    }

    /**
     * @param string $phone
     * @return string
     */
    protected function clearPhone(string $phone): string
    {
        return str_replace(['+', '-', ' ', '(', ')'], '', $phone);
    }

    /**
     * @param string $phone
     * @return void
     * @throws InvalidPhoneException
     */
    protected function validate(string $phone): void
    {
        if (false === filter_var($this->clearPhone($phone), FILTER_SANITIZE_NUMBER_INT)) {
            throw new InvalidPhoneException($phone);
        }
    }

    /**
     * @param string $phone
     * @return string
     */
    abstract protected function normalizeNumber(string $phone): string;

    /**
     * {@inheritDoc}
     */
    public function getValue(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}