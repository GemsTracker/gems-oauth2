<?php

namespace Gems\OAuth2\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Gems\OAuth2\Entity\EntityInterface;
use Gems\OAuth2\Entity\Group;
use Gems\OAuth2\Entity\Staff;
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

    protected string $delimiter = '@';

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * Get a user by email and group
     *
     * @param string $username
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
        $user = $this->getAuthUserByIdentifier($username);

        if ($user->verifyPassword($password)) {
            return $user;
        }

        throw OAuthServerException::accessDenied('No user with supplied credentials could be found');
    }

    public function getUserByIdentifier($username): EntityInterface|null
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

    public function getAuthUserByIdentifier(string|int $username): User
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder
            ->from(User::class, 'user')
            ->innerJoin(Staff::class, 'staff', Join::WITH, 'user.login = staff.loginName AND user.organizationId = staff.organizationId')
            ->innerJoin(Group::class, 'permissionGroup', Join::WITH, 'staff.group = permissionGroup.id')
            ->select(['user', 'permissionGroup.roleName', 'staff.id AS staffId']);

        if (is_int($username)) {
            $queryBuilder
                ->where('user.id = :id')
                ->setParameter('id', $username);
        } else {
            $userCredentials = explode(User::ID_SEPARATOR, $username);
            if (count($userCredentials) < 2) {
                throw OAuthServerException::accessDenied('No user with supplied credentials could be found');
            }
            list($username, $organizationId) = $userCredentials;

            $queryBuilder
                ->where('user.login = :login')
                ->andWhere('user.organizationId = :organizationId')
                ->setParameter('login', $username)
                ->setParameter('organizationId', $organizationId);
        }
        $userData = $queryBuilder->getQuery()->getOneOrNullResult();

        if (is_array($userData) && isset($userData[0], $userData['roleName'], $userData['staffId'])) {
            $user = $userData[0];
            $user->setRoleName($userData['roleName']);
            $user->setUserId($userData['staffId']);

            return $user;
        }

        throw OAuthServerException::accessDenied('No user with supplied credentials could be found');
    }
}