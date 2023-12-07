<?php

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table('gems__groups')]
class Group
{
    #[Id, GeneratedValue, Column('ggp_id_group', options: ['unsigned' => true])]
    protected int $id;

    #[Column(name: 'ggp_name', length: 30)]
    protected string $name;

    #[Column(name: 'ggp_role', length: 150)]
    protected string $roleName;

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->roleName;
    }


}