<?php

namespace Gems\OAuth2\Command;

use Gems\OAuth2\Util\PrivateKeyGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'oauth2:generate-keys', description: 'Generate public and private key for this application. Paths configured in the certificate namespace of config')]
class GenerateKeys extends Command
{
    protected static $defaultName = 'oauth2:generate-keys';

    private array $config;

    public function __construct(array $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (isset(
            $this->config['certificates'],
            $this->config['certificates']['private'],
            $this->config['certificates']['public']
        )) {
            $privateKeyLocation = $this->config['certificates']['private'];
            $publicKeyLocation = $this->config['certificates']['public'];

            $keyGenerator = new PrivateKeyGenerator($privateKeyLocation, $publicKeyLocation);
            $keyGenerator->generateKeys();

            $output->writeln(sprintf('<fg=green>Public key saved at:</> %s', $publicKeyLocation));
            $output->writeln(sprintf('<fg=green>Private key saved at:</> %s', $privateKeyLocation));

            return static::SUCCESS;
        }

        return static::FAILURE;
    }
}