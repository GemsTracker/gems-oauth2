<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gems\OAuth2\Repository\AccessTokenRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Lcobucci\JWT\Token;
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

    #[ManyToOne(targetEntity: User::class), JoinColumn(name: 'user_id', referencedColumnName: 'gul_id_user')]
    private User $user;

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
     * Generate a JWT from the access token
     *
     * @return Token
     */
    private function convertToJWT()
    {
        $this->initJwtConfiguration();

        return $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUser()->getReadableIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->accessToken;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the token user's identifier.
     *
     * @return string|int|null
     */
    public function getUserIdentifier(): int
    {
        return $this->user->getIdentifier();
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

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    // Method stub, as we use setUser instead
    public function setUserIdentifier($identifier): void
    {
    }
}
