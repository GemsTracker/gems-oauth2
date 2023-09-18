<?php

namespace Gems\OAuth2\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gems\OAuth2\Entity\EntityInterface;
use Gems\OAuth2\Repository\AccessTokenRepository;
use Gems\OAuth2\Repository\UserRepository;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;

class AccessTokenRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AccessTokenRepository
    {
        /**
         * @var EntityManagerInterface $entityManager
         */
        $entityManager = $container->get(EntityManagerInterface::class);
        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $container->get(UserRepositoryInterface::class);

        /**
         * @var ClassMetadata<EntityInterface> $classMetaData
         */
        $classMetaData = $entityManager->getClassMetadata(AccessTokenRepository::ENTITY);

        return new AccessTokenRepository($entityManager, $userRepository, $classMetaData);
    }
}