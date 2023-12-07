<?php

declare(strict_types=1);


namespace Gems\OAuth2\Repository;


use Gems\OAuth2\Entity\Client;
use Gems\OAuth2\Exception\AuthException;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository extends DoctrineEntityRepositoryAbstract implements ClientRepositoryInterface
{
    /**
     * Linked entity
     */
    public const ENTITY = Client::class;

    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     *
     * @return ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $filter = [
            'clientId' => $clientIdentifier,
            'active'  => 1,
        ];
        $client = $this->findOneBy($filter);

        if ($client === null) {
            throw new AuthException('Client with supplied user ID and secret not found');
        }
        if (!$client instanceof Client) {
            throw new AuthException(sprintf('Incorrect client entity class received. %s.', get_class($client)));
        }

        return $client;
    }

    /**
     * Validate a client's secret.
     *
     * @param string      $clientIdentifier The client's identifier
     * @param null|string $clientSecret     The client's secret (if sent)
     * @param null|string $grantType        The type of grant the client is using (if sent)
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        if ($clientSecret === null) {
            throw new AuthException('Client secret required');
        }
        try {
            $client = $this->getClientEntity($clientIdentifier);
        } catch (AuthException $e) {
            return false;
        }

        if (!$client instanceof Client || !password_verify($clientSecret, $client->getSecret())) {
            return false;
        }
        return true;
    }
}
