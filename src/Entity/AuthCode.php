<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Gems\OAuth2\Repository\AuthCodeRepository;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

#[Entity(repositoryClass: AuthCodeRepository::class), Table(name: 'gems__oauth_scopes')]
class AuthCode implements AuthCodeEntityInterface, EntityInterface
{
    use TokenData;

    #[Id, GeneratedValue,Column]
    private int $id;

    #[Column(length: 100)]
    private string $authCode;

    #[Column]
    private string $userId;

    #[Column]
    private string $clientId;

    #[Column(name: 'scopes', nullable: true)]
    protected ?string $scopeList;

    #[Column(nullable: true)]
    protected ?string $redirect;

    #[Column]
    private bool $revoked = false;

    #[Column]
    private \DateTimeImmutable $expiresAt;

    private ClientEntityInterface $client;

    public function getIdentifier(): string
    {
        return $this->authCode;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirect;
    }

    public function setIdentifier($authCode): void
    {
        $this->authCode = $authCode;
    }

    public function setRedirectUri($redirectUri): void
    {
        $this->redirect = $redirectUri;
    }
}
