<?php

namespace App\Tests\Domain;

use App\Domain\DepositId;
use App\Domain\Exception\DepositDoesNotExistException;
use App\Domain\Exception\DepositIsTooSmallException;
use App\Domain\Expense;
use App\Domain\Wish;
use App\Domain\WishId;
use App\Domain\WishName;
use DateInterval;
use DateTimeImmutable;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use App\Domain\Exception\WishIsFulfilledException;
use App\Domain\Exception\WishIsUnpublishedException;

class WishTest extends TestCase
{
    /**
     * @expectedException DepositIsTooSmallException
     */
    public function testMustDeclineDepositIfItIsLessThanFee()
    {
        $wish = $this->createWishWithPriceAndFee(1000, 100);
        $wish->publish();

        $wish->deposit(new Money(50, new Currency('USD')));
    }

    public function testExtraDepositMustFulfillTheWish()
    {
        $wish = $this->createWishWithPriceAndFund(1000, 900);
        $wish->publish();

        $wish->deposit(new Money(150, new Currency('USD')));

        static::assertTrue($wish->isFulfilled());
    }

    /**
     * @expectedException WishIsUnpublishedException
     */
    public function testMustNotDepositWhenUnpublished()
    {
        $wish = $this->createWishWithEmptyFund();
        $wish->deposit(new Money(100, new Currency('USD')));
    }

    /**
     * @expectedException WishIsFulfilledException
     */
    public function testMustNotDepositWhenFulfilled()
    {
        $fulfilled = $this->createWishWithPriceAndFund(500, 450);
        $fulfilled->publish();

        $fulfilled->deposit(new Money(100, new Currency('USD')));
        $fulfilled->deposit(new Money(100, new Currency('USD')));
    }

    public function testDepositShouldAddDepositToInternalCollection()
    {
        $wish = $this->createWishWithEmptyFund();
        $wish->publish();
        $depositMoney = new Money(150, new Currency('USD'));

        $wish->deposit($depositMoney);

        $deposits = $wish->getDeposits();
        static::assertCount(1, $deposits);
        static::assertArrayHasKey(0, $deposits);

        $deposit = $deposits[0];
        static::assertTrue($deposit->getMoney()->equals($depositMoney));
        static::assertSame($wish, $deposit->getWish());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDepositAndPriceCurrenciesMustMatch()
    {
        $wish = $this->createWishWithEmptyFund();
        $wish->publish();

        $wish->deposit(new Money(125, new Currency('RUB')));
    }

    private function createWishWithEmptyFund(): Wish
    {
        return new Wish(
            WishId::next(),
            new WishName('Bicycle'),
            Expense::fromCurrencyAndScalars(
                new Currency('USD'),
                1000,
                100
            )
        );
    }

    private function createWishWithPriceAndFund(int $price, int $fund): Wish
    {
        return new Wish(
            WishId::next(),
            new WishName('Bicycle'),
            Expense::fromCurrencyAndScalars(
                new Currency('USD'),
                $price,
                10,
                $fund
            )
        );
    }

    /**
     * @expectedException WishIsUnpublishedException
     */
    public function testMustNotWithdrawIfUnpublished()
    {
        $wish = $this->createWishWithPriceAndFund(500, 0);
        $wish->publish();
        $deposit = $wish->deposit(new Money(100, new Currency('USD')));
        $wish->unpublish();

        $wish->withdraw($deposit->getId());
    }

    /**
     * @expectedException WishIsFulfilledException
     */
    public function testMustNotWithdrawIfFulfilled()
    {
        $wish = $this->createWishWithPriceAndFund(500, 450);
        $wish->publish();
        $deposit = $wish->deposit(new Money(100, new Currency('USD')));

        $wish->withdraw($deposit->getId());
    }

    /**
     * @expectedException DepositDoesNotExistException
     */
    public function testWithdrawMustThrowOnNonExistentId()
    {
        $wish = $this->createWishWithEmptyFund();
        $wish->publish();

        $wish->withdraw(DepositId::next());
    }

    public function testWithdrawShouldRemoveDepositFromInternalCollection()
    {
        $wish = $this->createWishWithEmptyFund();
        $wish->publish();
        $wish->deposit(new Money(150, new Currency('USD')));

        $wish->withdraw($wish->getDeposits()[0]->getId());

        static::assertCount(0, $wish->getDeposits());
    }

    public function testSurplusFundsMustBe100()
    {
        $wish = $this->createWishWithPriceAndFund(500, 300);
        $wish->publish();

        $wish->deposit(new Money(100, new Currency('USD')));
        $wish->deposit(new Money(200, new Currency('USD')));

        $expected = new Money(100, new Currency('USD'));
        static::assertTrue($wish->calculateSurplusFunds()->equals($expected));
    }

    public function testSurplusFundsMustBeZero()
    {
        $wish = $this->createWishWithPriceAndFund(500, 250);
        $wish->publish();

        $wish->deposit(new Money(100, new Currency('USD')));

        $expected = new Money(0, new Currency('USD'));
        static::assertTrue($wish->calculateSurplusFunds()->equals($expected));
    }

    public function testFulfillmentDatePredictionBasedOnFee()
    {
        $price = 1500;
        $fee = 20;
        $wish = $this->createWishWithPriceAndFee($price, $fee);
        $daysToGo = ceil($price / $fee);

        $expected = (new DateTimeImmutable())->add(new DateInterval("P{$daysToGo}D"));

        static::assertEquals(
            $expected->getTimestamp(),
            $wish->predictFulfillmentDateBasedOnFee()->getTimestamp()
        );
    }

    public function testFulfillmentDatePredictionBasedOnFund()
    {
        $price = 1500;
        $fund = 250;
        $fee = 25;
        $wish = $this->createWish($price, $fee, $fund);
        $daysToGo = ceil(($price - $fund) / $fee);

        $expected = (new DateTimeImmutable())->add(new DateInterval("P{$daysToGo}D"));

        static::assertEquals(
            $expected->getTimestamp(),
            $wish->predictFulfillmentDateBasedOnFund()->getTimestamp()
        );
    }

    public function testPublishShouldPublishTheWish()
    {
        $wish = $this->createWishWithEmptyFund();
        $updatedAt = $wish->getUpdatedAt();

        $wish->publish();

        static::assertTrue($wish->isPublished());
        static::assertNotSame($updatedAt, $wish->getUpdatedAt());
    }

    public function testUnpublishShouldUnpublishTheWish()
    {
        $wish = $this->createWishWithEmptyFund();
        $updatedAt = $wish->getUpdatedAt();

        $wish->unpublish();

        static::assertFalse($wish->isPublished());
        static::assertNotSame($updatedAt, $wish->getUpdatedAt());
    }

    public function testChangePrice()
    {
        $wish = $this->createWishWithPriceAndFee(1000, 10);
        $expected = new Money(1500, new Currency('USD'));
        $updatedAt = $wish->getUpdatedAt();

        static::assertSame($updatedAt, $wish->getUpdatedAt());

        $wish->changePrice($expected);

        static::assertTrue($wish->getPrice()->equals($expected));
        static::assertNotSame($updatedAt, $wish->getUpdatedAt());
    }

    public function testChangeFee()
    {
        $wish = $this->createWishWithPriceAndFee(1000, 10);
        $expected = new Money(50, new Currency('USD'));
        $updatedAt = $wish->getUpdatedAt();

        static::assertSame($updatedAt, $wish->getUpdatedAt());

        $wish->changeFee($expected);

        static::assertTrue($wish->getFee()->equals($expected));
        static::assertNotSame($updatedAt, $wish->getUpdatedAt());
    }

    /**
     * @param int $price
     * @param int $fee
     * @return Wish
     */
    private function createWishWithPriceAndFee(int $price, int $fee): Wish
    {
        return new Wish(
            WishId::next(),
            new WishName('Bicycle'),
            Expense::fromCurrencyAndScalars(
                new Currency('USD'),
                $price,
                $fee
            )
        );
    }
}
