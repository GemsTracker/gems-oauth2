<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\GeneratedValue;
use Gems\OAuth2\Repository\AccessTokenRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

#[Entity(repositoryClass: AccessTokenRepository::class), Table(name: 'gems__oauth_access_tokens')]
class AccessToken implements AccessTokenEntityInterface, EntityInterface
{
    use AccessTokenTrait;
    use TokenData;

    #[Id, GeneratedValue, Column]
    private int $id;

    #[Column(length: 100)]
    private string $accessToken;

    #[Column]
    private string $userId;

    #[Column]
    private string $clientId;

    #[Column(name: 'scopes', nullable: true)]
    protected ?string $scopeList;

    #[Column]
    private bool $revoked;

    #[Column]
    private \DateTimeImmutable $expiresAt;

    private ClientEntityInterface $client;

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setIdentifier($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
