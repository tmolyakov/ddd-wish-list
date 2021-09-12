<?php

namespace App\Domain;

use Webmozart\Assert\Assert;

final class WishName
{
    /** @var string */
    private $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::notEmpty($name, 'Name must not be empty.');

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }
}
