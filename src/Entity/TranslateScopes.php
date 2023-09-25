<?php

declare(strict_types=1);


namespace Gems\OAuth2\Entity;


use League\OAuth2\Server\Entities\ScopeEntityInterface;

trait TranslateScopes
{
    /**
     * list of textual presentation of scopes
     *
     * @var string
     */
    protected ?string $scopeList;

    protected array $scopes = [];

    public function addScope(ScopeEntityInterface $scope)
    {
        $this->scopes[$scope->getIdentifier()] = $scope;
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @return string|null
     */
    public function getScopeList(): ?string
    {
        if (!isset($this->scopeList)) {
            $this->scopeList = null;

            if (count($this->scopes)) {
                $this->scopeList = join(',', array_keys($this->scopes));
            }
        }
        return $this->scopeList;
    }

    /**
     * @param string $scopeList
     */
    public function setScopeList(string $scopeList): void
    {
        $this->scopeList = $scopeList;
    }
}
