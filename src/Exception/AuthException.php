<?php

declare(strict_types=1);


namespace Gems\OAuth2\Exception;


class AuthException extends \Exception
{
    protected ?string $description;


    public function __construct($message = "", $description = "", $code = 0, \Throwable $previous = null)
    {
        $this->description = $description;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get description of Exception
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description of Exception
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


}
