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
    use ScopeTrait;

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
    public function getName(): string
    {
        return $this->name;
    }
}
