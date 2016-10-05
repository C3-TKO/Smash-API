<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TeamRepository extends EntityRepository
{
    public function findAllQueryBuilder($filter = '')
    {
        $qb = $this->createQueryBuilder('teams');

        if ($filter) {
            $qb->andWhere('teams.name LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }

        $qb->addOrderBy('teams.id');
        return $qb;
    }
}