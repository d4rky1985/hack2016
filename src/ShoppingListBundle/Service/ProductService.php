<?php
namespace ShoppingListBundle\Service;

use Doctrine\ORM\EntityManager;
use ShoppingListBundle\Entity\Products;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ShoppingListBundle\Repository\ProductsRepository;

/**
 * Class ProductService.
 */
class ProductService
{
    /** @const string */
    const ID = 'hack2016.product.service';

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

    public function getShoppingListProducts() {

        return;
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

    /**
     * @param $productId
     * @return ProductSuggestion
     */
    public function getRecommendedProduct($productId)
    {
        $product = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:ProductSuggestion')
            ->find($productId);

        if (empty($product)) {
            throw new NotFoundHttpException();
        }

        return $product;
    }
}
