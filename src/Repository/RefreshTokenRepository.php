<?php

declare(strict_types=1);

namespace Gems\OAuth2\Repository;

use Doctrine\ORM\Exception\ORMException;
use Gems\OAuth2\Entity\RefreshToken;
use Gems\OAuth2\Exception\AuthException;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository extends DoctrineEntityRepositoryAbstract implements RefreshTokenRepositoryInterface
{
    /**
     * Linked entity
     */
    public const ENTITY = RefreshToken::class;

    /**
     * @inheritDoc
     */
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setRevoked(false);

        return $refreshToken;
    }

    /**
     * @inheritDoc
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        try {
            $this->_em->persist($refreshTokenEntity);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new AuthException('Refresh token could not be saved');
        }
    }

    /**
     * @inheritDoc
     */
    public function revokeRefreshToken($tokenId): void
    {
        $refreshToken = $this->findOneBy(['id' => $tokenId]);
        $refreshToken->setRevoked(true);

        try {
            $this->_em->persist($refreshToken);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new AuthException('Refresh token could not be revoked');
        }
    }

    /**
     * @inheritDoc
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        if ($refreshToken = $this->findOneBy(['id' => $tokenId])) {
            return $refreshToken->getRevoked();
        }

        return false;
    }
}
