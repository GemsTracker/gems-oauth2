<?php

namespace Gems\OAuth2\Util;

use Gems\Helper\Env;
use Symfony\Component\Filesystem\Filesystem;

class PrivateKeyGenerator
{
    /**
     * @var int bit size of the key
     */
    protected int $bits = 4096;

    /**
     * @var int default key filemode
     */
    protected int $fileMode = 0600;

    public function __construct()
    {
    }

    public function generateKeys(): array
    {
        $config = [
            'private_key_bits' => $this->bits,
        ];

        // Fix for Windows bugs
        $configFile = Env::get('OPENSSL_CONF');
        if ($configFile) {
            $config['config'] = $configFile;
        }

        $resource = openssl_pkey_new($config);

        if (! $resource) {
            if (! $configFile) {
                echo "No Open SSL config set in environment by OPENSSL_CONF.\n";
            }
            die(openssl_error_string() . "\n");
        }
        $privateKey = null;

        openssl_pkey_export($resource, $privateKey, null, $config);
        if (! $privateKey) {
            die(openssl_error_string());
        }

        $publicKeyInfo = openssl_pkey_get_details($resource);
        $publicKey = $publicKeyInfo['key'];

        return [
            'public' => $publicKey,
            'private' => $privateKey,
        ];
    }

    public function generateKeyFiles(string $privateKeyLocation, string $publicKeyLocation): bool
    {
        $keys = $this->generateKeys();
        $filesystem = new Filesystem();

        try {
            $filesystem->dumpFile($privateKeyLocation, $keys['private']);
            $filesystem->chmod($privateKeyLocation, $this->fileMode);

            $filesystem->dumpFile($publicKeyLocation, $keys['public']);
            $filesystem->chmod($publicKeyLocation, $this->fileMode);

        } catch(\Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
    }
}