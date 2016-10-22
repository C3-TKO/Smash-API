<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RoundRepository extends EntityRepository
{
    public function findAllQueryBuilder($filter = '')
    {
        $qb = $this->createQueryBuilder('rounds');

        if ($filter) {
            $qb->andWhere('rounds.name LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }

        $qb->addOrderBy('rounds.id');
        return $qb;
    }
}