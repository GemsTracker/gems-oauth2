<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Gems\OAuth2\Repository\MfaCodeRepository;

#[Entity(repositoryClass: MfaCodeRepository::class), Table(name: 'gems__oauth_mfa_codes')]
class MfaCode implements MfaCodeEntityInterface, EntityInterface
{
    use TokenData;

    #[Id, GeneratedValue,Column]
    private int $id;

    #[Column(length: 100)]
    private string $mfaCode;

    #[Column(length: 32)]
    private string $authMethod;

    #[Column]
    private int $userId;

    #[Column]
    private string $clientId;

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

    /**
     * @param string $authMethod
     */
    public function setAuthMethod(string $authMethod): void
    {
        $this->authMethod = $authMethod;
    }

    /**
     * @param string $mfaCode
     */
    public function setIdentifier($mfaCode): void
    {
        $this->mfaCode = $mfaCode;
    }
}
