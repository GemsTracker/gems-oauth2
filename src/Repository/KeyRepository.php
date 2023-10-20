<?php

namespace Gems\OAuth2\Repository;

use League\OAuth2\Server\Exception\OAuthServerException;

class KeyRepository
{
    public function getCertificates(array $config, bool $includePrivateKey = true): array
    {
        $publicEnvKey = getenv('OAUTH2_PUBLIC_KEY') ? str_replace('\n', "\n", getenv('OAUTH2_PUBLIC_KEY')) : null;
        $privateEnvKey = getenv('OAUTH2_PRIVATE_KEY') ? str_replace('\n', "\n", getenv('OAUTH2_PRIVATE_KEY')) : null;
        $envPassPhrase = getenv('OAUTH2_KEY_PASSPHRASE') ? str_replace('\n', "\n", getenv('OAUTH2_KEY_PASSPHRASE')) : null;

        if ($publicEnvKey && $privateEnvKey) {
            return [
                'public' => $publicEnvKey,
                'private' => $includePrivateKey ? $privateEnvKey : null,
                'passPhrase' => $envPassPhrase,
            ];
        }

        $publicConfigKey = $config['certificates']['public'] ?? null;
        $privateConfigKey = $config['certificates']['private'] ?? null;
        $configPassPhrase = $config['certificates']['passPhrase'] ?? null;

        if ($publicConfigKey && $privateConfigKey) {
            return [
                'public' => $publicConfigKey,
                'private' => $includePrivateKey ? $privateConfigKey : null,
                'passPhrase' => $configPassPhrase,
            ];
        }

        throw new OAuthServerException('No certificates found', 404, 'cert_missing');
    }
}