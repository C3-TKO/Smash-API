<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PlayerRepository extends EntityRepository
{
    public function findAllQueryBuilder($filter = '')
    {
        $qb = $this->createQueryBuilder('player');

        if ($filter) {
            $qb->andWhere('player.name LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }

        $qb->addOrderBy('player.id');
        return $qb;
    }
}