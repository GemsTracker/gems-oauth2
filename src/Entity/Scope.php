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
    private int $id; // @phpstan-ignore property.unused

    #[Column]
    private string $name;

    #[Column]
    private string $description; // @phpstan-ignore property.unused

    #[Column]
    private bool $active; // @phpstan-ignore property.unused

    public function getIdentifier(): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
}
