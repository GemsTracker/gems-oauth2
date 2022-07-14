<?php


namespace Gems\OAuth2\Factory;

use Gems\OAuth2\Repository\AccessTokenRepository;
use Gems\OAuth2\Repository\ClientRepository;
use Gems\OAuth2\Repository\ScopeRepository;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use League\OAuth2\Server\AuthorizationServer;

class AuthorizationServerFactory implements FactoryInterface
{
    /**
     * @var AuthorizationServer
     */
    protected $server;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthorizationServer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthorizationServer
    {
        $config = $container->get('config');

        $accessTokenRepository = $container->get(AccessTokenRepository::class);
        $clientRepository = $container->get(ClientRepository::class);
        $scopeRepository = $container->get(ScopeRepository::class);

        $this->server = new AuthorizationServer($clientRepository, $accessTokenRepository, $scopeRepository, $config['certificates']['private'], $config['app_key']);

        if(isset($config['oauth2']['grants'])) {
            $this->addGrants($config['oauth2']['grants'], $container);
        }

        return $this->server;
    }

    /**
     * Add the current enabled grants from config to the Authorization server
     *
     * @param array $grants
     * @param ContainerInterface $container
     * @throws \Exception
     */
    protected function addGrants(array $grants, ContainerInterface $container)
    {
        if (isset($grants['authorization_code'], $grants['authorization_code']['class'])) {

            if (isset($grants['authorization_code']['token_valid'])) {
                $valid = new \DateInterval($grants['authorization_code']['token_valid']);
            } else {
                $valid = new \DateInterval('PT1H');
            }

            // Enable the client credentials grant on the server
            $this->server->enableGrantType(
                $container->get($grants['authorization_code']['class']),
                $valid // access tokens will expire after 1 hour
            );
        }
        if (isset($grants['client_credentials'], $grants['client_credentials']['class'])) {

            if (isset($grants['client_credentials']['token_valid'])) {
                $valid = new \DateInterval($grants['client_credentials']['token_valid']);
            } else {
                $valid = new \DateInterval('PT1H');
            }

            // Enable the client credentials grant on the server
            $this->server->enableGrantType(
                $container->get($grants['client_credentials']['class']),
                $valid // access tokens will expire after 1 hour
            );
        }
        if (isset($grants['password'], $grants['password']['class'])) {

            if (isset($grants['password']['token_valid'])) {
                $valid = new \DateInterval($grants['password']['token_valid']);
            } else {
                $valid = new \DateInterval('PT1H');
            }

            // Enable the client credentials grant on the server
            $this->server->enableGrantType(
                $container->get($grants['password']['class']),
                $valid // access tokens will expire after 1 hour
            );
        }
        if (isset($grants['refresh_token'], $grants['refresh_token']['class'])) {

            if (isset($grants['refresh_token']['token_valid'])) {
                $valid = new \DateInterval($grants['refresh_token']['token_valid']);
            } else {
                $valid = new \DateInterval('PT1H');
            }

            // Enable the client credentials grant on the server
            $this->server->enableGrantType(
                $container->get($grants['refresh_token']['class']),
                $valid // access tokens will expire after 1 hour
            );
        }
        if (isset($grants['mfa_code'], $grants['mfa_code']['class'])) {

            if (isset($grants['mfa_code']['token_valid'])) {
                $valid = new \DateInterval($grants['mfa_code']['token_valid']);
            } else {
                $valid = new \DateInterval('PT1H');
            }

            // Enable the client credentials grant on the server
            $this->server->enableGrantType(
                $container->get($grants['mfa_code']['class']),
                $valid // access tokens will expire after 1 hour
            );
        }
    }
}
