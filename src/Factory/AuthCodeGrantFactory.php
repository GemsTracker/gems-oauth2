<?php

declare(strict_types=1);

namespace Gems\OAuth2\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Psr\Container\ContainerInterface;

class AuthCodeGrantFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthCodeGrant
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthCodeGrant
    {
        $authCodeRepository = $container->get(AuthCodeRepositoryInterface::class);
        $refreshTokenRepository = $container->get(RefreshTokenRepositoryInterface::class);
        $config = $container->get('config');


        $valid = new \DateInterval('PT10M');
        if (isset(

            $config['oauth2'],
            $config['oauth2']['grants'],
            $config['oauth2']['grants']['authorization_code'],
            $config['oauth2']['grants']['authorization_code']['code_valid'])
        ) {
            $valid = new \DateInterval($config['oauth2']['grants']['authorization_code']['code_valid']);
        }

        $authCodeGrant = new AuthCodeGrant($authCodeRepository, $refreshTokenRepository, $valid);

        return $authCodeGrant;
    }
}
