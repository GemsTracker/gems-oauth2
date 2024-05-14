<?php

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'gems__user_passwords')]
class UserPassword implements EntityInterface
{

    #[Id, Column(name: 'gup_id_user', options: ['unsigned' => true])]
    private int $userId;


    #[Column(name: 'gup_password', nullable: true)]
    private string|null $password;

    /**
     * @return string
     */
    public function getPassword(): string|null
    {
        return $this->password;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}