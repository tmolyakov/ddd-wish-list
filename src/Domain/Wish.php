<?php

namespace App\Domain;

use App\Domain\Exception\DepositDoesNotExistException;
use App\Domain\Exception\DepositIsTooSmallException;
use App\Domain\Exception\WishIsFulfilledException;
use App\Domain\Exception\WishIsUnpublishedException;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Money\Money;
use Webmozart\Assert\Assert;

class Wish
{
    private AbstractId $id;
    private WishName $name;
    private Expense $expense;
    private ArrayCollection $deposits;
    private bool $published = false;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /**
     * @param AbstractId $id
     * @param WishName $name
     * @param Expense $expense
     * @param DateTimeImmutable|null $createdAt
     */
    public function __construct(
        AbstractId $id,
        WishName $name,
        Expense $expense,
        DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->expense = $expense;
        $this->deposits = new ArrayCollection();
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $createdAt ?? new DateTimeImmutable();
    }

    /**
     * @param Money $amount
     * @return Deposit
     * @throws DepositIsTooSmallException
     * @throws WishIsFulfilledException
     * @throws WishIsUnpublishedException
     */
    public function deposit(Money $amount): Deposit
    {
        $this->assertCanDeposit($amount);

        $deposit = new Deposit(DepositId::next(), $this, $amount);
        $this->deposits->add($deposit);

        return $deposit;
    }

    /**
     * @param Money $amount
     * @throws DepositIsTooSmallException
     * @throws WishIsFulfilledException
     * @throws WishIsUnpublishedException
     */
    private function assertCanDeposit(Money $amount)
    {
        if (!$this->published) {
            throw new WishIsUnpublishedException($this->getId());
        }

        if ($this->isFulfilled()) {
            throw new WishIsFulfilledException($this->getId());
        }

        if ($amount->lessThan($this->getFee())) {
            throw new DepositIsTooSmallException($amount, $this->getFee());
        }

        Assert::true(
            $amount->isSameCurrency($this->expense->getPrice()),
            'Deposit currency must match the price\'s one.'
        );
    }

    /**
     * @return bool
     */
    public function isFulfilled(): bool
    {
        return $this->getFund()->greaterThanOrEqual($this->expense->getPrice());
    }

    /**
     * @return void
     */
    public function publish(): void
    {
        $this->published = true;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return void
     */
    public function unpublish(): void
    {
        $this->published = false;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return Money
     */
    public function getFund(): Money
    {
        return array_reduce($this->deposits->toArray(), function (Money $fund, Deposit $deposit) {
            return $fund->add($deposit->getMoney());
        }, $this->expense->getInitialFund());
    }

    /**
     * @param AbstractId $depositId
     * @throws DepositDoesNotExistException
     * @throws WishIsFulfilledException
     * @throws WishIsUnpublishedException
     */
    public function withdraw(AbstractId $depositId)
    {
        $this->assertCanWithdraw();

        $deposit = $this->getDepositById($depositId);
        $this->deposits->removeElement($deposit);
    }

    /**
     * @throws WishIsFulfilledException
     * @throws WishIsUnpublishedException
     */
    private function assertCanWithdraw()
    {
        if (!$this->published) {
            throw new WishIsUnpublishedException($this->getId());
        }

        if ($this->isFulfilled()) {
            throw new WishIsFulfilledException($this->getId());
        }
    }

    /**
     * @param AbstractId $depositId
     * @return Deposit
     * @throws DepositDoesNotExistException
     */
    private function getDepositById(AbstractId $depositId): Deposit
    {
        $deposit = $this->deposits->filter(
            function (Deposit $deposit) use ($depositId) {
                return $deposit->getId()->equalTo($depositId);
            }
        )->first();

        if (!$deposit) {
            throw new DepositDoesNotExistException($depositId);
        }

        return $deposit;
    }

    /**
     * @return Money
     */
    public function calculateSurplusFunds(): Money
    {
        $difference = $this->getPrice()->subtract($this->getFund());

        return $difference->isNegative()
            ? $difference->absolute()
            : new Money(0, $this->getCurrency());
    }

    public function predictFulfillmentDateBasedOnFee(): DateTimeInterface
    {
        $daysToGo = ceil(
            $this->getPrice()
                ->divide($this->getFee()->getAmount())
                ->getAmount()
        );

        return $this->createFutureDate($daysToGo);
    }

    public function predictFulfillmentDateBasedOnFund(): DateTimeInterface
    {
        $daysToGo = ceil(
            $this->getPrice()
                ->subtract($this->getFund())
                ->divide($this->getFee()->getAmount())
                ->getAmount()
        );

        return $this->createFutureDate($daysToGo);
    }

    private function createFutureDate($daysToGo): DateTimeInterface
    {
        return (new DateTimeImmutable())->add(new DateInterval("P{$daysToGo}D"));
    }

    public function changePrice(Money $amount)
    {
        $this->expense = $this->expense->changePrice($amount);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function changeFee(Money $amount)
    {
        $this->expense = $this->expense->changeFee($amount);
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return AbstractId
     */
    public function getId(): AbstractId
    {
        return $this->id;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->expense->getPrice();
    }

    /**
     * @return Money
     */
    public function getFee(): Money
    {
        return $this->expense->getFee();
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published;
    }
}
