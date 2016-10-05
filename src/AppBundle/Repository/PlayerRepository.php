<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PlayerRepository extends EntityRepository
{
    public function findAllQueryBuilder($filter = '')
    {
        $qb = $this->createQueryBuilder('players');

        if ($filter) {
            $qb->andWhere('players.name LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }

        $qb->addOrderBy('players.id');
        return $qb;
    }
}