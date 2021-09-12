<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Client;
use App\Domain\Repository\ClientRepositoryInterface;
use App\Domain\ValueObject\ID;

class ClientService implements ClientServiceInterface
{
    /**
     * @var ClientRepositoryInterface
     */
    protected ClientRepositoryInterface $repository;

    public function __construct(ClientRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Client $client): Client
    {
        return $this->repository->save($client);
    }

    /**
     * {@inheritdoc}
     */
    public function get(ID $ID): ?Client
    {
        return $this->repository->getById($ID);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Client $client): void
    {
        $this->repository->delete($client);
    }
}
