<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @package UserBundle\Repository
 */
class UserRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getUsersToNotify()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.fbToken IS NOT NULL')
            ->andWhere('u.fbId IS NOT NULL');

        return $qb->getQuery()->getResult();
    }

}