<?php

namespace Gems\OAuth2;

use Gems\Factory\MonologFactory;
use Gems\OAuth2\Command\GenerateKeyFiles;
use Gems\OAuth2\Command\GenerateKeys;
use Gems\OAuth2\Factory\AccessTokenRepositoryFactory;
use Gems\OAuth2\Factory\AuthCodeGrantFactory;
use Gems\OAuth2\Factory\AuthorizationServerFactory;
use Gems\OAuth2\Factory\DoctrineRepositoryFactory;
use Gems\OAuth2\Factory\PasswordGrantFactory;
use Gems\OAuth2\Factory\ResourceServerFactory;
use Gems\OAuth2\Grant\MfaCodeGrant;
use Gems\OAuth2\Grant\PasswordGrant;
use Gems\OAuth2\Handler\AccessTokenHandler;
use Gems\OAuth2\Repository\AccessTokenRepository;
use Gems\OAuth2\Repository\AuthCodeRepository;
use Gems\OAuth2\Repository\ClientRepository;
use Gems\OAuth2\Repository\MfaCodeRepository;
use Gems\OAuth2\Repository\MfaCodeRepositoryInterface;
use Gems\OAuth2\Repository\RefreshTokenRepository;
use Gems\OAuth2\Repository\ScopeRepository;
use Gems\OAuth2\Repository\UserRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Log\LogLevel;

class ConfigProvider
{
    public const OAUTH_LOGGER = 'oauth-log';

    /**
     * @return mixed[]
     */
    public function __invoke(): array
    {
        return [
            'certificates' => $this->getCertificateSettings(),
            'console' => $this->getConsoleSettings(),
            'doctrine' => $this->getDoctrineSettings(),
            'dependencies'  => $this->getDependencies(),
            'log'           => $this->getLoggers(),
            'migrations'    => $this->getMigrations(),
            'oauth2'   => $this->getOAuth2Settings(),
            'routes' => $this->getRoutes(),
        ];
    }

    /**
     * @return mixed[]
     */
    public function getCertificateSettings(): array
    {
        $rootDir = getcwd();

        return [
            'public' => $rootDir . '/data/keys/gems.public.key',
            'private' => $rootDir . '/data/keys/gems.private.key',
            'keyPermissionsCheck' => true, // On Windows systems set this to false to disable checking file permissions
            'passPhrase'          => null  // Optionally set the passphrase to use
        ];
    }

    /**
     * @return mixed[]
     */
    public function getConsoleSettings(): array
    {
        return [
            'commands' => [
                GenerateKeys::class,
                GenerateKeyFiles::class,
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                // OAuth Servers
                AuthorizationServer::class => AuthorizationServerFactory::class,
                ResourceServer::class => ResourceServerFactory::class,

                // OAuth Entity repositories
                AccessTokenRepository::class => AccessTokenRepositoryFactory::class,
                AuthCodeRepository::class => DoctrineRepositoryFactory::class,
                ClientRepository::class => DoctrineRepositoryFactory::class,
                RefreshTokenRepository::class => DoctrineRepositoryFactory::class,
                ScopeRepository::class => DoctrineRepositoryFactory::class,
                UserRepository::class => DoctrineRepositoryFactory::class,
                MfaCodeRepository::class => DoctrineRepositoryFactory::class,

                // OAuth Grants
                AuthCodeGrant::class => AuthCodeGrantFactory::class,
                PasswordGrant::class => PasswordGrantFactory::class,
                MfaCodeGrant::class => PasswordGrantFactory::class,
            ],
            'aliases' => [
                AccessTokenRepositoryInterface::class => AccessTokenRepository::class,
                AuthCodeRepositoryInterface::class => AuthCodeRepository::class,
                ClientRepositoryInterface::class => ClientRepository::class,
                RefreshTokenRepositoryInterface::class => RefreshTokenRepository::class,
                ScopeRepositoryInterface::class => ScopeRepository::class,
                UserRepositoryInterface::class => UserRepository::class,
                MfaCodeRepositoryInterface::class => MfaCodeRepository::class,
            ],
        ];
    }

    /**
     * List with doctrine settings
     *
     * @return mixed[]
     */
    public function getDoctrineSettings(): array
    {
        return [
            'paths' => [
                __DIR__ . '/Entity',
            ]
        ];
    }

    /**
     * @return mixed[]
     */
    public function getMigrations(): array
    {
        $migrations = [
            'tables' => [
                __DIR__ . '/../configs/db/tables',
            ],
        ];

        if (getenv('APP_ENV') === 'development') {
            $migrations['seeds'][] = [
                'db' => 'gems',
                'path' => __DIR__ . '/../configs/dev/db/seeds',
                'module' => 'gems-oauth2-dev',
            ];
        }

        return $migrations;
    }

    protected function getLoggers(): array
    {
        return [
            static::OAUTH_LOGGER => [
                'writers' => [
                    'stream' => [
                        'name' => 'stream',
                        'priority' => LogLevel::DEBUG,
                        'options' => [
                            'stream' => 'data/logs/oauth.log',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * List of OAuth2 settings
     *
     * @return mixed[]
     */
    public function getOAuth2Settings(): array
    {
        return [
            'grants' => [
                /*'authorization_code' => [
                    'class' => AuthCodeGrant::class,
                    'code_valid' => 'PT10M', // Time an auth code can be exchanged for a token
                    'token_valid' => 'PT1H', // Time a token is valid
                ],*/

                'password' => [
                    'class' => PasswordGrant::class,
                    'token_valid' => 'PT1H', // Time a token is valid
                ],
                'refresh_token' => [
                    'class' => RefreshTokenGrant::class,
                    'token_valid' => 'PT1H', // Time a token is valid
                ],
                'mfa_code' => [
                    'class' => MfaCodeGrant::class,
                    'code_valid' => 'PT10M', // Time an auth code can be exchanged for a token
                    'token_valid' => 'PT1H', // Time a token is valid
                ],
                /*'client_credentials' => [
                    'class' => ClientCredentialsGrant::class,
                    'token_valid' => 'PT1H', // Time a token is valid
                ],*/
            ],
        ];
    }

    public function getRoutes(): array
    {
        return [
            [
                'name' => 'access_token',
                'path' => '/api/access_token',
                'middleware' => [
                    AccessTokenHandler::class,
                ],
                'allowed_methods' => ['POST'],
            ],
        ];
    }
}