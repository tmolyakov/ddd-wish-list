<?php

namespace App\Infrastructure\Controller\Api;

use App\Application\Service\ClientServiceInterface;
use App\Domain\ValueObject\ID;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientController extends AbstractController
{
    /** @var ClientServiceInterface  */
    protected ClientServiceInterface $clientService;

    /**
     * @param ClientServiceInterface $clientService
     */
    public function __construct(ClientServiceInterface $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * @param int $id
     *
     * @return object
     */
    public function getClient(int $id): object
    {
        $id = new ID($id);

        return $this->clientService->get($id);
    }
}
