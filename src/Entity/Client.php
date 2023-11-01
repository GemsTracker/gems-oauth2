<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Gems\OAuth2\Repository\ClientRepository;
use Gems\OAuth2\Repository\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;

#[Entity(repositoryClass: ClientRepository::class), Table(name: 'gems__oauth_clients')]
class Client implements ClientEntityInterface, EntityInterface
{
    #[Id, GeneratedValue,Column]
    private int $id; // @phpstan-ignore property.unused

    #[Column]
    private string $clientId;

    #[Column]
    private string $name;

    #[Column]
    private string $secret;

    #[Column]
    private bool $active; // @phpstan-ignore property.unused

    #[Column]
    private bool $confidential;

    #[Column(nullable: true)]
    private ?string $redirect = '';

    public function getIdentifier(): string
    {
        return $this->clientId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRedirectUri(): array
    {
        if ($this->redirect === null) {
            return [];
        }
        return explode(',', $this->redirect);
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return bool
     */
    public function isConfidential(): bool
    {
        return $this->confidential;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @param bool $confidential
     */
    public function setConfidential(bool $confidential): void
    {
        $this->confidential = $confidential;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $redirect
     */
    public function setRedirect(?string $redirect): void
    {
        $this->redirect = $redirect;
    }
}
