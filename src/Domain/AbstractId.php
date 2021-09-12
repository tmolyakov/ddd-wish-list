<?php

namespace App\Domain;

use App\Domain\Exception\InvalidIdentityException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractId
{
    /** @var UuidInterface */
    protected $id;

    /**
     * @param UuidInterface $id
     */
    private function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $id
     *
     * @return AbstractId
     * @throws InvalidIdentityException
     */
    public static function fromString(string $id): AbstractId
    {
        try {
            return new static(Uuid::fromString($id));
        } catch (InvalidUuidStringException $exception) {
            throw new InvalidIdentityException($id);
        }
    }

    /**
     * @return AbstractId
     */
    public static function next(): AbstractId
    {
        return new static(Uuid::uuid4());
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id->toString();
    }

    /**
     * @param AbstractId $id
     *
     * @return bool
     */
    public function equalTo(AbstractId $id): bool
    {
        return $this->getId() === $id->getId();
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
