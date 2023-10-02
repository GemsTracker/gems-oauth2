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

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $keyGenerator = new PrivateKeyGenerator();
        $keys = $keyGenerator->generateKeys();

        foreach($keys as $keyName => $key) {
            $output->writeln(sprintf('%s: %s', $keyName, $key));
        }

        return static::SUCCESS;
    }
}