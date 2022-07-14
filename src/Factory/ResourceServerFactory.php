<?php

declare(strict_types=1);


namespace Gems\OAuth2\Factory;


use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\CryptKey;
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

        $certificates        = $config['certificates'];
        $passPhrase          = array_key_exists('passPhrase', $certificates) ? $certificates['passPhrase'] : null;
        $keyPermissionsCheck = array_key_exists('keyPermissionsCheck', $certificates) ? $certificates['keyPermissionsCheck'] : true;

        $publicKey = new CryptKey($certificates['public'], $passPhrase, $keyPermissionsCheck);

        $validator = new BearerTokenValidator($accessTokenRepository);

        return new ResourceServer($accessTokenRepository, $publicKey, $validator);
    }
}
