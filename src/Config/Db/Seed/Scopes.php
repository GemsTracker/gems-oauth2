<?php


namespace Gems\OAuth2\Config\Db\Seed;

use Gems\Db\Migration\SeedInterface;

class Scopes implements SeedInterface
{
    public function getDescription(): string|null
    {
        return 'Default OAuth2 scope';
    }

    public function getOrder(): int
    {
        return 1000;
    }

    public function __invoke(): array
    {
        return [
            'gems__oauth_scopes' => [
                [
                    'name' => 'all',
                    'description' => 'Everything',
                    'active' => 1,
                ],
            ],
        ];
    }
}
