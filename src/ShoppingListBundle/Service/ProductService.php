<?php
namespace ShoppingListBundle\Service;

use Doctrine\ORM\EntityManager;
use ShoppingListBundle\Entity\Products;
use ShoppingListBundle\Repository\ProductsBoughtRepository;
use ShoppingListBundle\Entity\ProductsBought;
use ShoppingListBundle\Entity\ProductsSuggestions;
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

    /**
     * @return array
     */
    public function getShoppingListProducts() {
        /** @var ProductsRepository $productsRepository */
        $productsRepository = $this->getEntityManager()->getRepository('ShoppingListBundle:Products');

        /** @var ProductsBoughtRepository $productsBoughtRepository */
        $productsBoughtRepository = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:ProductsBought');

        $shoppingListProducts = $productsRepository->getAllProducts();

        $productsListNotBought = [];
        $productsListBought = [];

        /** @var Products $product */
        foreach($shoppingListProducts as $product) {
            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'image' => $product->getImage(),
                'quantity' => $productsBoughtRepository->getProductQuantity($product->getId()),
                'type' => $product->getType(),
            ];
            if($product->getStatus() == Products::STATUS_NOT_BOUGHT) {
                $productsListNotBought[] = $productData;
                continue;
            }
            $productsListBought[] = $productData;
        }

        return ['productsListNotBought' => $productsListNotBought, 'productsListBought' => $productsListBought];
    }

    /**
     * @param string $name
     * @return Products
     */
    public function getProductByName($name)
    {
        /** @var Products $product */
        $product = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:Products')
            ->findOneBy(array('name' => $name));
        if (is_null($product)) {
            $product = new Products();
            $product->setName($name);
        }

        return $product;
    }

    /**
     * @param int $productId
     * @return Products
     */
    public function getProductByReccomandation($productId)
    {
        /** @var ProductsSuggestions $recommandation */
        $recommandation = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:ProductsSuggestions')
            ->find($productId);

        /** @var Products $product */
        $product = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:Products')
            ->findOneBy(array('name' => $recommandation->getName()));

        if (!is_null($product)) {
            return $product;
        }

        $product = new Products();
        $product->setName($recommandation->getName());
        $product->setShortDescription($recommandation->getShortDescription());
        $product->setDescription($recommandation->getDescription());
        $product->setUrl($recommandation->getUrl());
        $product->setImage($recommandation->getImage());

        return $product;
    }

    /**
     * @param $name
     * @param $productId
     */
    public function saveProduct($name, $productId)
    {
        $product = $productId == 0 ? $this->getProductByName($name) : $this->getProductByReccomandation($productId);
        $product->setStatus(Products::STATUS_NOT_BOUGHT);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $productId
     * @return ProductsSuggestions
     */
    public function getRecommendedProduct($productId)
    {
        $product = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:ProductsSuggestions')
            ->find($productId);

        if (empty($product)) {
            throw new NotFoundHttpException();
        }

        return $product;
    }

    public function getSortedShopingListProducts()
    {
        /** @var ProductsBoughtRepository $productsBoughtRepository */
        $productsBoughtRepository = $this->getEntityManager()->getRepository('ShoppingListBundle:ProductsBought');

        $productsBought = $productsBoughtRepository->getProductsBoughtOrder();

        $products = array();

        /** @var ProductsBought $productBought */
        foreach ($productsBought as $productBought) {
            $products[$productBought->getParent()->getId()] = $productBought->getProduct();
        }
        //todo show products sorted

    }
}
