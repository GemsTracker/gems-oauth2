<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Gems\OAuth2\Repository\UserRepository;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

#[Entity(repositoryClass: UserRepository::class), Table(name: 'gems__user_logins')]
class User implements UserEntityInterface, EntityInterface
{
    public const ID_SEPARATOR = '@';

    use EntityTrait;

    #[Id, GeneratedValue, Column(name: 'gul_id_user')]
    protected int $id;

    #[Column(name: 'gul_login', length: 30)]
    protected string $login;

    #[Column(name: 'gul_id_organization')]
    protected int $organizationId;

    public function getIdentifier()
    {
        return $this->id;
    }

    public function getReadableIdentifier(): string
    {
        return $this->getLogin() . static::ID_SEPARATOR . $this->getOrganizationId();
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
