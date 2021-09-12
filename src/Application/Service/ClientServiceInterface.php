<?php

namespace App\Application\Service;

use App\Domain\Entity\Client;
use App\Domain\ValueObject\ID;

interface ClientServiceInterface
{
    /**
     * @param Client $client
     * @return Client
     */
    public function save(Client $client): Client;

    /**
     * @param ID $ID
     * @return Client|null
     */
    public function get(ID $ID): ?Client;

    /**
     * @param Client $client
     * @return void
     */
    public function delete(Client $client): void;
}
