<?php

namespace Gems\OAuth2\Repository;

use Gems\OAuth2\Entity\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository extends DoctrineEntityRepositoryAbstract implements UserRepositoryInterface
{
    /**
     * Linked entity
     */
    public const ENTITY = User::class;

    /**
     * Get a user by email and group
     *
     * @param string $username
     * @param string $group
     * @return User
     * @throws OAuthServerException
     */
    public function getUser(string $username, int $organizationId): User
    {
        $filter = [
            'login'             => $username,
            'organizationId'    => $organizationId,
        ];
        $user = $this->findOneBy($filter);
        if (!$user instanceof User) {
            throw OAuthServerException::accessDenied('No user with supplied credentials could be found');
        }
        $user->setIdentifier($user->getLogin() . User::ID_SEPARATOR . $user->getOrganizationId());

        return $user;
    }

    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity): User
    {
        $user = $this->getUserByIdentifier($username);

        // TODO ADD Authentication check!
        if ($user instanceof User) {
            return $user;
        }

        throw OAuthServerException::accessDenied('No user with supplied credentials could be found');
    }

    public function getUserByIdentifier($username): User
    {
        if (is_numeric($username)) {
            return $this->find($username);
        }

        $userCredentials = explode(User::ID_SEPARATOR, $username);
        if (count($userCredentials) == 2) {
            list($username, $organizationId) = $userCredentials;
            return $this->getUser($username, (int)$organizationId);
        }

        throw OAuthServerException::accessDenied('No user with supplied credentials could be found');
    }
}