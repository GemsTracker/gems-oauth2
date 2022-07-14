<?php

declare(strict_types=1);

namespace Gems\OAuth2\Repository;

use Doctrine\ORM\Exception\ORMException;
use Gems\OAuth2\Entity\MfaCode;
use Gems\OAuth2\Entity\MfaCodeEntityInterface;
use Gems\OAuth2\Exception\AuthException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

class MfaCodeRepository extends DoctrineEntityRepositoryAbstract implements MfaCodeRepositoryInterface
{
    /**
     * Linked Entity
     */
    public const ENTITY = MfaCode::class;

    /**
     * Creates a new MfaCode
     *
     * @return MfaCodeEntityInterface
     */
    public function getNewMfaCode(): MfaCodeEntityInterface
    {
        $mfaCode = new MfaCode();
        $mfaCode->setRevoked(false);

        return $mfaCode;
    }

    /**
     * Persists a new mfa code to permanent storage.
     *
     * @param MfaCodeEntityInterface $mfaCodeEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewMfaCode(MfaCodeEntityInterface $mfaCodeEntity): void
    {
        try {
            $this->_em->persist($mfaCodeEntity);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new AuthException('Mfa code could not be saved');
        }
    }

    /**
     * Revoke an mfa code.
     *
     * @param string $codeId
     */
    public function revokeMfaCode($codeId): void
    {
        $mfaCode = $this->findOneBy(['id' => $codeId]);
        $mfaCode->setRevoked(true);

        try {
            $this->_em->persist($mfaCode);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new AuthException('Mfa code could not be revoked');
        }
    }

    /**
     * Check if the mfa code has been revoked.
     *
     * @param string $codeId
     *
     * @return bool Return true if this code has been revoked
     */
    public function isMfaCodeRevoked($codeId): bool
    {
        if ($mfaToken = $this->findOneBy(['id' => $codeId])) {
            return $mfaToken->isRevoked();
        }

        return false;
    }
}
