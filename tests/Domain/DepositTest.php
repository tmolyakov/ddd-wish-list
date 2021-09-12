<?php

namespace App\Tests\Domain;

use InvalidArgumentException;
use Mockery;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use App\Domain\Deposit;
use App\Domain\DepositId;
use App\Domain\Wish;

class DepositTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testDepositAmountMustNotBeZero()
    {
        $wish = Mockery::mock(Wish::class);
        $amount = new Money(0, new Currency('USD'));

        new Deposit(DepositId::next(), $wish, $amount);
    }
}
