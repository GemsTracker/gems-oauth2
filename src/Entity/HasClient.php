<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use League\OAuth2\Server\Entities\ClientEntityInterface;

trait HasClient
{
    #[Column]
    protected string $clientId;

    protected ClientEntityInterface $client;

    /**
     * Get the client that the token was issued to.
     *
     * @return ClientEntityInterface
     */
    public function getClient(): ClientEntityInterface
    {
        return $this->client;
    }

    /**
     * Get the client ID that the token was issued to.
     *
     * @return int client id
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Set the client that the token was issued to.
     *
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
        $this->clientId = $client->getIdentifier();
    }
}
