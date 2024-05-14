<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Gems\OAuth2\Repository\ScopeRepository;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

#[Entity(repositoryClass: ScopeRepository::class), Table(name: 'gems__oauth_scopes')]
class Scope implements ScopeEntityInterface, EntityInterface
{
    #[Id, GeneratedValue,Column]
    private int $id;

    #[Column]
    private string $name;

    #[Column]
    private string $description;

    #[Column]
    private bool $active;

    public function getIdentifier(): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Serialize the object to the scopes string identifier when using json_encode().
     *
     * @return string
     */
    public function jsonSerialize(): mixed
    {
        return $this->getIdentifier();
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
