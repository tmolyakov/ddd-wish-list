<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Client;
use App\Domain\ValueObject\ID;

interface ClientRepositoryInterface
{
    /**
     * @param ID $id
     * @return Client|null
     */
    public function getById(ID $id): ?Client;

    /**
     * @param Client $client
     * @return Client
     */
    public function save(Client $client): Client;

    /**
     * @param Client $client
     * @return void
     */
    public function delete(Client $client): void;
}
