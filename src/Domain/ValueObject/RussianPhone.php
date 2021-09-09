<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\Exception\InvalidPhoneNumberLengthException;
use App\Domain\ValueObject\Exception\PhoneNumberShouldStartWithException;

class RussianPhone extends Phone
{
    protected const NUMBER_LENGTH = 11;

    protected const FIRST_DIGIT = ['7', '8'];

    /**
     * {@inheritDoc}
     */
    protected function validate(string $phone): void
    {
        parent::validate($phone);

        $this->checkLength($phone);
        $this->checkFirstDigit($phone);
    }

    /**
     * {@inheritDoc}
     */
    protected function normalizeNumber(string $phone): string
    {
        $phone = $this->clearPhone($phone);
        $area = substr($phone, 1, 3);
        $prefix = substr($phone, 4, 3);
        $number = substr($phone, 7, 4);

        return sprintf('+7 (%s)-%s-%s', $area, $prefix, $number);
    }

    /**
     * @param string $phone
     * @return void
     * @throws InvalidPhoneNumberLengthException
     */
    protected function checkLength(string $phone): void
    {
        $length = strlen($this->clearPhone($phone));

        if (self::NUMBER_LENGTH !== $length) {
            throw new InvalidPhoneNumberLengthException($phone, self::NUMBER_LENGTH, $length);
        }
    }

    /**
     * @param string $phone
     * @throws PhoneNumberShouldStartWithException
     */
    protected function checkFirstDigit(string $phone): void
    {
        $firstChar = substr($this->clearPhone($phone), 0, 1);
        if (!in_array($firstChar, self::FIRST_DIGIT)) {
            throw new PhoneNumberShouldStartWithException($phone, self::FIRST_DIGIT);
        }
    }
}
