<?php

declare(strict_types=1);

namespace Gems\OAuth2\Repository;

use Gems\OAuth2\Entity\AccessToken;
use Gems\OAuth2\Exception\AuthException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Exception\ORMException;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class AccessTokenRepository extends DoctrineEntityRepositoryAbstract implements AccessTokenRepositoryInterface
{
    /**
     * Linked entity
     */
    public const ENTITY = AccessToken::class;

    protected LoggerInterface $userActionLog;
    /**
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, ClassMetadata $metaData)
    {
        parent::__construct($entityManager, $metaData);
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new AccessToken();
        $accessToken->setRevoked(false);
        $accessToken->setClient($clientEntity);
        foreach($scopes as $scope) {
            $accessToken->addScope($scope);
        }


        $user = $this->userRepository->getUserByIdentifier($userIdentifier);
        $accessToken->setUser($user);

        //$this->userActionLog->info('user.login', ['user' => $user->getId(), 'client' => $clientEntity->getIdentifier()]);

        return $accessToken;
    }

    /**
     * @inheritDoc
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        try {
            $this->_em->persist($accessTokenEntity);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new AuthException('Access token could not be saved');
        }
    }

    /**
     * @inheritDoc
     */
    public function revokeAccessToken($tokenId): void
    {
        $accessToken = $this->findOneBy(['accessToken' => $tokenId]);
        $accessToken->setRevoked(true);

        try {
            $this->_em->persist($accessToken);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new AuthException('Access token could not be revoked');
        }
    }

    /**
     * @inheritDoc
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        if ($accessToken = $this->findOneBy(['accessToken' => $tokenId])) {
            return $accessToken->getRevoked();
        }

        return false;
    }
}
