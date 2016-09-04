<?php
namespace ShoppingListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use ShoppingListBundle\Entity\Products;

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
            ->andWhere('p.status LIKE :status')
            ->setParameter('status', Products::STATUS_BOUGHT)
            ->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getAllProducts() : array
    {
        return $this->createQueryBuilder('p')
            ->addOrderBy('p.type', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()->getResult();
    }
}