<?php

namespace Gems\OAuth2\Log;

use Monolog\Logger;

class OAuthLogger extends Logger
{
    public const NAME = 'oauth-logger';
    public function __construct(
    ) {
        parent::__construct(static::NAME);
    }
}