<?php

declare(strict_types=1);


namespace Gems\OAuth2\Factory;


use Gems\OAuth2\Repository\MfaCodeRepositoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;

class PasswordGrantFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $userRepository = $container->get(UserRepositoryInterface::class);

        $refreshTokenRepository = $container->get(RefreshTokenRepositoryInterface::class);
        $config = $container->get('config');

        if (isset(

            $config['oauth2'],
            $config['oauth2']['grants'],
            $config['oauth2']['grants']['mfa_code'])
        ) {
            $valid = new \DateInterval('PT10M');
            if (isset($config['oauth2']['grants']['mfa_code']['code_valid'])) {
                $valid = new \DateInterval($config['oauth2']['grants']['mfa_code']['code_valid']);
            }
            $mfaCodeRepository = $container->get(MfaCodeRepositoryInterface::class);
            $passwordGrant = new $requestedName($userRepository, $refreshTokenRepository, $mfaCodeRepository, $valid);

            return $passwordGrant;
        }

        $passwordGrant = new PasswordGrant($userRepository, $refreshTokenRepository);

        return $passwordGrant;
    }
}
