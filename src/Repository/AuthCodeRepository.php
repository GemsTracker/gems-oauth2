<?php

declare(strict_types=1);


namespace Gems\OAuth2\Repository;

use Doctrine\ORM\Exception\ORMException;
use Gems\OAuth2\Entity\AuthCode;
use Gems\OAuth2\Exception\AuthException;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository extends DoctrineEntityRepositoryAbstract implements AuthCodeRepositoryInterface
{
    /**
     * Linked entity
     */
    public const ENTITY = AuthCode::class;

    /**
     * Creates a new AuthCode
     *
     * @return AuthCodeEntityInterface
     */
    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        $authCode = new AuthCode();
        $authCode->setRevoked(false);

        return $authCode;
    }

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param AuthCodeEntityInterface $authCodeEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        try {
            $this->_em->persist($authCodeEntity);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new AuthException('Auth code could not be saved');
        }
    }

    /**
     * Revoke an auth code.
     *
     * @param string $codeId
     */
    public function revokeAuthCode($codeId): void
    {
        $filter = [
            'id' => $codeId,
        ];

        $authCode = $this->findOneBy($filter);
        if (!$authCode instanceof AuthCode) {
            throw new AuthException('Auth code could not be revoked');
        }

        $authCode->setRevoked(true);

        try {
            $this->_em->persist($authCode);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new AuthException('Auth code could not be revoked');
        }
    }

    /**
     * Check if the auth code has been revoked.
     *
     * @param string $codeId
     *
     * @return bool Return true if this code has been revoked
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        $filter = [
            'id' => $codeId,
        ];

        $authCode = $this->findOneBy($filter);

        if ($authCode instanceof AuthCode) {
            return $authCode->isRevoked();
        }

        return false;
    }
}
