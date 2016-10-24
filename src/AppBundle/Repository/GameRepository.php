<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository
{
    public function findAllQueryBuilder($filter = '')
    {
        $qb = $this->createQueryBuilder('games');

        /**
         * @TODO: Implement a filter for a specific date
         * @TODO: Implement a filter for rounds within two specific dates

        if ($filter) {
            $qb->andWhere('rounds.date LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }
         */

        $qb->addOrderBy('games.id');
        return $qb;
    }
}