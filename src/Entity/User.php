<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Gems\OAuth2\Repository\UserRepository;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

#[Entity(repositoryClass: UserRepository::class), Table(name: 'gems__users')]
class User implements UserEntityInterface, EntityInterface
{
    public const ID_SEPARATOR = '::';

    use EntityTrait;

    #[Column(name: 'gul_login', length: 30)]
    protected string $login;

    #[Column(name: 'gul_id_organization')]
    protected int $organizationId;

    public function getIdentifier()
    {
        return $this->login . static::ID_SEPARATOR . $this->organizationId;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return int
     */
    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }
}
