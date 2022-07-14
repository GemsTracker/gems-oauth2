<?php

namespace Gems\OAuth2\Util;

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

    /**
     * Location for the private key
     *
     * @var string
     */
    protected string $privateKeyLocation;

    /**
     * Location for the private key
     *
     * @var string
     */
    protected string $publicKeyLocation;

    public function __construct(string $privateKeyLocation, string $publicKeyLocation)
    {
        $this->privateKeyLocation = $privateKeyLocation;
        $this->publicKeyLocation = $publicKeyLocation;
    }

    public function generateKeys(): bool
    {
        $config = [
            'private_key_bits' => $this->bits,
        ];
        $resource = openssl_pkey_new($config);

        $privateKey = null;

        openssl_pkey_export($resource, $privateKey);

        $publicKeyInfo = openssl_pkey_get_details($resource);
        $publicKey = $publicKeyInfo['key'];

        $filesystem = new Filesystem();

        try {
            $filesystem->dumpFile($this->privateKeyLocation, $privateKey);
            $filesystem->chmod($this->privateKeyLocation, $this->fileMode);

            $filesystem->dumpFile($this->publicKeyLocation, $publicKey);
            $filesystem->chmod($this->publicKeyLocation, $this->fileMode);

        } catch(\Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
    }
}