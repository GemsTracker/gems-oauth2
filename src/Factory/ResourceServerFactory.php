<?php

declare(strict_types=1);


namespace Gems\OAuth2\Factory;


use Gems\OAuth2\AuthorizationValidators\BearerTokenValidator;
use Gems\OAuth2\Repository\KeyRepository;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\ResourceServer;

class ResourceServerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ResourceServer|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $accessTokenRepository = $container->get(AccessTokenRepositoryInterface::class);

        $config = $container->get('config');

        $keyRepository = new KeyRepository();
        $certificates = $keyRepository->getCertificates($config, false);

        $keyPermissionsCheck = $config['certificates']['keyPermissionsCheck'] ?? null;

        $publicKey = new CryptKey($certificates['public'], $certificates['passPhrase'], $keyPermissionsCheck);

        $validator = new BearerTokenValidator($accessTokenRepository);

        return new ResourceServer($accessTokenRepository, $publicKey, $validator);
    }
}
