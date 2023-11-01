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
use Gems\OAuth2\Repository\MfaCodeRepository;

#[Entity(repositoryClass: MfaCodeRepository::class), Table(name: 'gems__oauth_mfa_codes')]
class MfaCode implements MfaCodeEntityInterface, EntityInterface
{
    use TokenData;

    #[Id, GeneratedValue,Column]
    private int $id; // @phpstan-ignore property.unused

    #[Column(length: 100)]
    private string $mfaCode;

    #[Column(length: 32)]
    private string $authMethod;

    #[ManyToOne(targetEntity: User::class), JoinColumn(name: 'user_id', referencedColumnName: 'gul_id_user')]
    private User $user;

    #[Column]
    private string $clientId; // @phpstan-ignore property.onlyWritten

    #[Column]
    protected ?string $scopeList;

    #[Column]
    private bool $revoked;

    private \DateTimeImmutable $expiresAt;

    /**
     * @return string
     */
    public function getAuthMethod(): string
    {
        return $this->authMethod;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->mfaCode;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserIdentifier(): int
    {
        return $this->user->getIdentifier();
    }

    /**
     * @param string $method
     */
    public function setAuthMethod(string $method): void
    {
        $this->authMethod = $method;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier): void
    {
        $this->mfaCode = (string)$identifier;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    // Method stub, as we use setUser instead
    public function setUserIdentifier($identifier): void
    {
    }
}
