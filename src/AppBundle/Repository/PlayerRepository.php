<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Player;

class PlayerRepository extends EntityRepository
{
    public function findAllQueryBuilder()
    {
        $qb = $this->createQueryBuilder('player');
        $qb->addOrderBy('player.id');
        return $qb;
    }
}