<?php

namespace Gems\OAuth2\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Gems\OAuth2\Repository\AccessTokenRepository;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;

class AccessTokenRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AccessTokenRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $userRepository = $container->get(UserRepositoryInterface::class);
        $classMetaData = $entityManager->getClassMetadata(AccessTokenRepository::ENTITY);

        return new AccessTokenRepository($entityManager, $userRepository, $classMetaData);
    }
}