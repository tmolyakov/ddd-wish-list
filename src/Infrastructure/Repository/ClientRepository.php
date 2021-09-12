<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Client as DomainClient;
use App\Domain\Repository\ClientRepositoryInterface;
use App\Domain\ValueObject\ID;
use App\Infrastructure\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     *  {@inheritdoc}
     */
    public function getById(ID $id): ?DomainClient
    {
        return $this->find($id->getValue());
    }

    /**
     *  {@inheritdoc}
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(DomainClient $client): DomainClient
    {
        if (!($client instanceof Client)) {
            throw new \LogicException('Exception instanceof');
        }

        $this->getEntityManager()->persist($client);
        $this->getEntityManager()->flush();

        return $client;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(DomainClient $client): void
    {
        if (!($client instanceof Client)) {
            throw new \LogicException('Exception instanceof');
        }

        $this->getEntityManager()->remove($client);
        $this->getEntityManager()->flush();
    }
}
