<?php

namespace App\Domain;

use Money\Currency;
use Money\Money;
use Webmozart\Assert\Assert;

final class Expense
{
    /** @var Money  */
    private Money $price;

    /** @var Money  */
    private Money $fee;

    /** @var Money  */
    private Money $initialFund;

    /**
     * @param Money $price
     * @param Money $fee
     * @param Money $initialFund
     */
    private function __construct(Money $price, Money $fee, Money $initialFund)
    {
        $this->price = $price;
        $this->fee = $fee;
        $this->initialFund = $initialFund;
    }

    /**
     * @param Currency $currency
     * @param int $price
     * @param int $fee
     * @param int|null $initialFund
     *
     * @return Expense
     */
    public static function fromCurrencyAndScalars(
        Currency $currency,
        int $price,
        int $fee,
        int $initialFund = null
    ): Expense
    {
        foreach ([$price, $fee] as $argument) {
            Assert::notEmpty($argument);
            Assert::greaterThan($argument, 0);
        }

        Assert::lessThan($fee, $price, 'Fee must be less than price.');

        if (null !== $initialFund) {
            Assert::greaterThanEq($initialFund, 0);
            Assert::lessThan($initialFund, $price, 'Initial fund must be less than price.');
        }

        return new self(
            new Money($price, $currency),
            new Money($fee, $currency),
            new Money($initialFund ?? 0, $currency)
        );
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->price->getCurrency();
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @param Money $amount
     * @return $this
     */
    public function changePrice(Money $amount): Expense
    {
        Assert::true($amount->getCurrency()->equals($this->getCurrency()));

        return new self($amount, $this->fee, $this->initialFund);
    }

    /**
     * @return Money
     */
    public function getFee(): Money
    {
        return $this->fee;
    }

    /**
     * @param Money $amount
     * @return $this
     */
    public function changeFee(Money $amount): Expense
    {
        Assert::true($amount->getCurrency()->equals($this->getCurrency()));

        return new self($this->price, $amount, $this->initialFund);
    }

    /**
     * @return Money
     */
    public function getInitialFund(): Money
    {
        return $this->initialFund;
    }
}
