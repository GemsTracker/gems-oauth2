<?php

namespace Gems\OAuth2\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class DoctrineRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): object
    {
        /**
         * @var EntityManagerInterface $entityManager
         */
        $entityManager = $container->get(EntityManagerInterface::class);

        $entityClass = null;
        if ($options && isset($options['entity'])) {
            $entityClass = $options['entity'];
        } elseif (defined($requestedName . '::ENTITY')) {
            $entityClass = $requestedName::ENTITY;
        }

        if ($entityClass === null) {
            throw new \RuntimeException('No Entity class found for ' . $requestedName);
        }

        $classMetaData = $entityManager->getClassMetadata($requestedName::ENTITY);

        return new $requestedName($entityManager, $classMetaData);
    }
}