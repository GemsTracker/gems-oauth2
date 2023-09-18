<?php

declare(strict_types=1);

namespace Gems\OAuth2\Repository;

use Doctrine\ORM\EntityRepository;
use Gems\OAuth2\Entity\EntityInterface;

/**
 * @template-extends EntityRepository<EntityInterface>
 */
class DoctrineEntityRepositoryAbstract extends EntityRepository
{
}
