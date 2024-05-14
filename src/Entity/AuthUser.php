<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Gems\OAuth2\Repository\UserRepository;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

#[Entity(repositoryClass: UserRepository::class), Table(name: 'gems__user_logins')]
class AuthUser extends User
{
    #[OneToOne(targetEntity: UserPassword::class)]
    #[JoinColumn(name: 'gul_id_user', referencedColumnName: 'gup_id_user', nullable: false)]
    protected UserPassword $password;

    protected string|null $roleName = null;

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password->getPassword());
    }

    public function getRoleName(): string|null
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): void
    {
        $this->roleName = $roleName;
    }
}
