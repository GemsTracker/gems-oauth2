<?php

declare(strict_types=1);

namespace Gems\OAuth2\Repository;

use Gems\OAuth2\Entity\Scope;
use Gems\OAuth2\Exception\AuthException;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository extends DoctrineEntityRepositoryAbstract implements ScopeRepositoryInterface
{
    const ENTITY = Scope::class;

    /**
     * @inheritDoc
     */
    public function getScopeEntityByIdentifier($identifier): object
    {
        $filter = [
            'name'      => $identifier,
            'active'    => 1,
        ];
        $scope = $this->findOneBy($filter);

        if ($scope === null) {
            throw new AuthException(sprintf('Scope with identifier %s could not be found', $identifier));
        }
        if (!$scope instanceof Scope) {
            throw new AuthException(sprintf('Incorrect scope entity class received. %s. Expected: %s', get_class($scope), static::ENTITY));
        }

        return $scope;
    }

    /**
     * @inheritDoc
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null): array
    {
        return $scopes;
    }
}
