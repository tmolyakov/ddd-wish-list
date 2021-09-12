<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\ClientServiceInterface;
use App\Domain\Entity\Client;
use App\Domain\Factory\ClientFactoryInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\PersonName;
use App\Domain\ValueObject\RussianPhone;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    protected function setUp(): void
    {
        parent::bootKernel();

        $this->entityManager = self::$container
            ->get('doctrine')
            ->getManager();

        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->entityManager->getConnection()->rollBack();
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test(): void
    {
        /** @var ClientServiceInterface $clientService */
        $clientService = self::$container
            ->get(ClientServiceInterface::class);

        /** @var ClientFactoryInterface $factory */
        $factory = self::$container
            ->get(ClientFactoryInterface::class);

        $client = $clientService->save(
            $factory
                ->setName(new PersonName('Иванов Иван Иванович'))
                ->setEmail(new Email('mail@mail.ru'))
                ->setPhone(new RussianPhone('+7-922-743-22-11'))
                ->setAddress('address')
                ->make()
        );

        $this->assertInstanceOf(Client::class, $client);

        $this->assertSame('Иванов Иван Иванович', (string)$client->getName());
        $this->assertSame('Иван', (string)$client->getName()->getFirstName());
        $this->assertSame('Иванов', (string)$client->getName()->getLastName());
        $this->assertSame('Иванович', (string)$client->getName()->getMiddleName());

        $id = $client->getId();

        $client = $clientService->get($id);

        $this->assertInstanceOf(Client::class, $client);

        $clientService->delete($client);

        $client = $clientService->get($id);

        $this->assertNull($client);
    }
}
