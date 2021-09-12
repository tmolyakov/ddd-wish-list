<?php

namespace App\Domain;

use DateTimeImmutable;
use DateTimeInterface;
use Money\Money;
use Webmozart\Assert\Assert;

class Deposit
{
    private AbstractId $id;
    private Wish $wish;
    private Money $amount;
    private DateTimeImmutable $createdAt;

    /**
     * @param AbstractId $id
     * @param Wish $wish
     * @param Money $amount
     */
    public function __construct(AbstractId $id, Wish $wish, Money $amount)
    {
        Assert::false($amount->isZero(), 'Deposit must not be empty.');

        $this->id = $id;
        $this->wish = $wish;
        $this->amount = $amount;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return AbstractId
     */
    public function getId(): AbstractId
    {
        return $this->id;
    }

    /**
     * @return Wish
     */
    public function getWish(): Wish
    {
        return $this->wish;
    }

    /**
     * @return Money
     */
    public function getMoney(): Money
    {
        return $this->amount;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
