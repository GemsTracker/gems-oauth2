<?php

namespace Gems\OAuth2\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Gems\OAuth2\Repository\AccessTokenRepository;
use Gems\OAuth2\Repository\RefreshTokenRepository;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class RefreshTokenRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RefreshTokenRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $classMetaData = $entityManager->getClassMetadata(RefreshTokenRepository::ENTITY);

        return new RefreshTokenRepository($entityManager, $classMetaData);
    }
}