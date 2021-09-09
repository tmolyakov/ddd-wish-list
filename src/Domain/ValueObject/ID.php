<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class ID extends AbstractValueObject
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->id = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): int
    {
        return $this->id;
    }
}