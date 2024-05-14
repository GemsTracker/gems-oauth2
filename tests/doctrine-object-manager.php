<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

$dsnParser = new DsnParser();
$connectionParams = $dsnParser
    ->parse('pdo-sqlite:///:memory:');

$connection = DriverManager::getConnection($connectionParams);

$paths = [
    '../src/Entity',
];

$config = ORMSetup::createAttributeMetadataConfiguration($paths, true);

return new EntityManager($connection, $config);