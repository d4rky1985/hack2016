<?php
namespace ShoppingListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProductsRepository extends EntityRepository
{
    /**
     * @param string $name
     * @return array
     */
    public function findByNameLike($name) : array
    {
        return $this->createQueryBuilder('p')
            ->select('p.name')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()->getResult();
    }
}