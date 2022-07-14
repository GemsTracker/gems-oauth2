<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Gems\OAuth2\Repository\RefreshTokenRepository;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

#[Entity(repositoryClass: RefreshTokenRepository::class), Table(name: 'gems__oauth_refresh_tokens', )]
class RefreshToken implements RefreshTokenEntityInterface, EntityInterface
{
    #[Id, GeneratedValue,Column]
    private int $id;

    #[Column]
    private string $refreshToken;

    #[ManyToOne(targetEntity: AccessToken::class), JoinColumn(name: 'access_token', referencedColumnName: 'access_token')]
    private AccessTokenEntityInterface $accessToken;

    #[Column]
    private bool $revoked;

    #[Column]
    private \DateTimeImmutable $expiresAt;

    /**
     * @return AccessTokenEntityInterface
     */
    public function getAccessToken(): AccessTokenEntityInterface
    {
        return $this->accessToken;
    }

    public function getExpiryDateTime(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * @param AccessTokenEntityInterface $accessToken
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param \DateTimeImmutable $expiresAt
     */
    public function setExpiryDateTime(\DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @param string $refreshToken
     */
    public function setIdentifier($refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param bool $revoked
     */
    public function setRevoked(bool $revoked): void
    {
        $this->revoked = $revoked;
    }
}
