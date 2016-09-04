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
    public function getShoppingListProducts()
    {
        /** @var ProductsRepository $productsRepository */
        $productsRepository = $this->getEntityManager()->getRepository('ShoppingListBundle:Products');

        /** @var ProductsBoughtRepository $productsBoughtRepository */
        $productsBoughtRepository = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:ProductsBought');

        $shoppingListProducts = $productsRepository->getAllProducts();

        $shoppingListProducts = $this->getSortedShopingListProducts($shoppingListProducts);

        $productsListNotBought = [];
        $productsListBought = [];

        /** @var Products $product */
        foreach ($shoppingListProducts as $product) {
            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'image' => $product->getImage(),
                'quantity' => $productsBoughtRepository->getProductQuantity($product->getId()),
                'type' => $product->getType(),
            ];
            if ($product->getStatus() == Products::STATUS_NOT_BOUGHT) {
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
        $recommandation = $this->getRecommendedProduct($productId);

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

    /**
     * Get products for that we must send reminder
     *
     * @return array
     */
    public function getReccomendedNotificationProducts()
    {
        $productIds = array();
        $products = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:Products')
            ->findAll();

        /** @var Products $product */
        foreach ($products as $product) {
            $mustSendNotification = $this->getMustSendNotificationForProduct($product);
            if ($mustSendNotification) {
                $productIds[] = $product->getId();
            }
        }

        return $productIds;
    }

    /**
     * @param Products $product
     * @return bool
     */
    public function getMustSendNotificationForProduct(Products $product)
    {
        $boughts = $this->getEntityManager()
            ->getRepository('ShoppingListBundle:ProductsBought')
            ->findBy(array('product' => $product), array('buyingDate' => 'DESC'));
        if (count($boughts) < 2) {
            return false;
        }

        $days = $this->getNumberOfDays($boughts);
        /** @var \DateTime $lastBought */
        $lastBought = $boughts[0]->getBuyingDate();
        $now = new \DateTime('now');

        return $now->format('Y-m-d') == $lastBought->modify("+" . $days . " days")->format('Y-m-d');
    }

    /**
     * @param array $boughts
     * @return array
     */
    public function getNumberOfDays($boughts)
    {
        $days = array();
        /** @var ProductsBought $bought */
        foreach ($boughts as $cnt => $bought) {
            if ($cnt == count($boughts) - 1) {
                break;
            }
            $interval = $boughts[$cnt+1]->getBuyingDate()->diff($bought->getBuyingDate());
            $day = (int)$interval->format('%a');
            if ($day >= 1) {
                $days[] = $day;
            }
        }

        return (int)(array_sum($days)/count($days));
    }

    /**
     * @param $shoppingListProducts
     *
     * @return array
     */
    public function getSortedShopingListProducts($shoppingListProducts)
    {
        /** @var ProductsBoughtRepository $productsBoughtRepository */
        $productsBoughtRepository = $this->getEntityManager()->getRepository('ShoppingListBundle:ProductsBought');

        $productsBought = $productsBoughtRepository->getProductsBoughtOrder();

        $products = array();

        /** @var ProductsBought $productBought */
        foreach ($productsBought as $productBought) {
            if ($productBought->getParent() instanceof Products) {
                $products[$productBought->getParent()->getId()] = $productBought->getProduct();
            }
        }

        $productsBought = array();
        $productsNotBought = array();
        $sortedProducts = array();
        /** @var Products $product */
        foreach ($shoppingListProducts as $product) {
            if ($product->getStatus() == Products::STATUS_NOT_BOUGHT) {
                $productsNotBought[] = $product;
                if ($product->getType() == Products::TYPE_FAV) {
                    $sortedProducts[] = $product;
                } else {
                    continue;
                }
            } else {
                $productsBought[] = $product;
            }
        }

        /**
         * @var int $productId
         * @var Products $product
         */
        foreach ($products as $productId => $product) {
            if (in_array($product, $productsNotBought) && !in_array($product, $sortedProducts)) {
                $sortedProducts[] = $product;
            }
        }

        /** @var Products $product */
        foreach ($productsNotBought as $product) {
            if (!in_array($product, $sortedProducts)) {
                $sortedProducts[] = $product;
            }
        }

        $sortedProducts = array_merge($sortedProducts, $productsBought);

        return $sortedProducts;
    }
}
