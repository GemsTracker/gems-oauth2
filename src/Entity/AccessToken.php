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

    public const SESSION_KEY_CLAIM_NAME = 'session_key';

    #[Id, GeneratedValue, Column]
    private int $id;

    #[Column(length: 100)]
    private string $accessToken;

    #[ManyToOne(targetEntity: User::class), JoinColumn(name: 'user_id', referencedColumnName: 'gul_id_user', nullable: false)]
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
     * @var bool Add the current user session key to the access token
     */
    private bool $addSessionKey = false;

    /**
     * Generate a JWT from the access token
     *
     * @return Token
     */
    private function convertToJWT()
    {
        $this->initJwtConfiguration();

        $user = $this->getUser();

        $jwt = $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo($user->getReadableIdentifier())
            ->withClaim('user_id', $user->getIdentifier())
            ->withClaim('role', $user->getRoleName())
            ->withClaim('scopes', $this->getScopes());

        if ($this->addSessionKey) {
            $jwt = $jwt->withClaim(static::SESSION_KEY_CLAIM_NAME, $user->getSessionKey());
        }

        return $jwt->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    public function addSessionKey(): void
    {
        $this->addSessionKey = true;
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
     * @return int|string
     */
    public function getUserIdentifier(): int|string
    {
        return $this->user->getIdentifier();
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier): void
    {
        $this->accessToken = (string)$identifier;
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

    /**
     * Generate a string representation from the access token
     */
    public function __toString()
    {
        return $this->convertToJWT()->toString();
    }
}
