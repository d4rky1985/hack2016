<?php
namespace ShoppingListBundle\Service;

use Doctrine\ORM\EntityManager;
use ShoppingListBundle\Entity\Products;

/**
 * Class ProductService.
 */
class ProductService
{
    /** @var  EntityManager */
    protected $entityManager;

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function saveProduct($name)
    {
        /** @var Products $product */
        $product = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:Products')
            ->findOneBy(array('name' => $name));
        if (is_null($product)) {
            $product = new Products();
            $product->setName($name);
        }
        $product->setStatus(Products::STATUS_NOT_BOUGHT);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();

        return true;
    }
}
