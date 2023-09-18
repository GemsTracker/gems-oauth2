<?php

declare(strict_types=1);

namespace Gems\OAuth2\Repository;

use Gems\OAuth2\Entity\MfaCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

interface MfaCodeRepositoryInterface
{
    /**
     * Creates a new MfaCode
     *
     * @return MfaCodeEntityInterface
     */
    public function getNewMfaCode(): MfaCodeEntityInterface;

    /**
     * Persists a new mfa code to permanent storage.
     *
     * @param MfaCodeEntityInterface $mfaCodeEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewMfaCode(MfaCodeEntityInterface $mfaCodeEntity): void;

    /**
     * Revoke an mfa code.
     *
     * @param string $codeId
     */
    public function revokeMfaCode($codeId): void;

    /**
     * Check if the mfa code has been revoked.
     *
     * @param string $codeId
     *
     * @return bool Return true if this code has been revoked
     */
    public function isMfaCodeRevoked($codeId): bool;
}
