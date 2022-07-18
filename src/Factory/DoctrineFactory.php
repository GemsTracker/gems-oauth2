<?php

declare(strict_types=1);


namespace Gems\OAuth2\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\Mapping\Driver\StaticPHPDriver;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Cache\CacheItemPoolInterface;

class DoctrineFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EntityManagerInterface
     * @throws \Doctrine\ORM\ORMException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EntityManagerInterface
    {
        $config = $container->get('config');

        $paths = array_column($config['doctrine'], 'path');
        $isDevMode = false;
        if (isset($config['app'], $config['app']['env']) && $config['app']['env'] === 'development') {
            $isDevMode = true;
        }

        $databaseOptions = [
            'driver' => strtolower($config['db']['driver']),
            'host' => $config['db']['host'],
            'user' => $config['db']['username'],
            'password' => $config['db']['password'],
            'dbname' => $config['db']['database'],
        ];

        $config = ORMSetup::createConfiguration($isDevMode);
        $driver = new AttributeDriver($paths);
        $config->setMetadataDriverImpl($driver);

        $cache = $container->get(CacheItemPoolInterface::class);
        $config->setMetadataCache($cache);
        $config->setQueryCache($cache);
        $config->setResultCache($cache);
        $namingStrategy = new UnderscoreNamingStrategy(CASE_LOWER, true);
        $config->setNamingStrategy($namingStrategy);
        $entityManager = EntityManager::create($databaseOptions, $config);

        return $entityManager;
    }
}
