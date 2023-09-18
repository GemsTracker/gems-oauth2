<?php

declare(strict_types=1);

namespace Gems\OAuth2\Entity;

use League\OAuth2\Server\Entities\TokenInterface;

interface MfaCodeEntityInterface extends TokenInterface
{
    public function getAuthMethod(): string;

    public function setAuthMethod(string $method): void;

    public function setUser(User $user): void;
}
