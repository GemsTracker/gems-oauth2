<?php

namespace Gems\OAuth2\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'gems__staff')]
class Staff
{
    #[Id, Column(name: 'gsf_id_user', options: ['unsigned' => true]),]
    protected int $id;

    #[Column(name: 'gsf_login', length: 20)]
    protected string $loginName;

    #[Column(name: 'gsf_id_organization')]
    protected int $organizationId;

    #[OneToOne(targetEntity: Group::class)]
    #[JoinColumn(name: 'gsf_id_primary_group', referencedColumnName: 'ggp_id_group')]
    protected Group $group;

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }


}