<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

abstract class AbstractValueObject
{
    /**
     * @return mixed
     */
    abstract public function getValue();
}