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

    #[Column(name: 'gul_enable_2factor')]
    protected bool $mfaEnabled;

    #[OneToOne(targetEntity: UserPassword::class)]
    #[JoinColumn(name: 'gul_id_user', referencedColumnName: 'gup_id_user')]
    protected UserPassword|null $password = null;

    protected string|null $roleName = null;

    protected string|null $userId = null;

    #[Column(name: 'gul_session_key', nullable: true)]
    protected string|null $sessionKey;

    public function getIdentifier(): int
    {
        return $this->id;
    }

    public function getReadableIdentifier(): string
    {
        return $this->getLogin() . static::ID_SEPARATOR . $this->getOrganizationId();
    }

    public function getRoleName(): string|null
    {
        return $this->roleName;
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

    public function getSessionKey(): string
    {
        return $this->sessionKey;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function isMfaEnabled(): bool
    {
        return $this->mfaEnabled;
    }

    public function setMfaEnabled(bool $mfaEnabled): void
    {
        $this->mfaEnabled = $mfaEnabled;
    }

    public function setRoleName(string $roleName): void
    {
        $this->roleName = $roleName;
    }

    public function verifyPassword(string $password): bool
    {
        if ($this->password) {
            return password_verify($password, $this->password->getPassword());
        }
        return false;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }
}
