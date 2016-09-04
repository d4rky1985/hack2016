<?php
namespace ShoppingListBundle\Service;

use Doctrine\ORM\EntityManager;
use ShoppingListBundle\Entity\Products;
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

    public function getShoppingListProducts() {

        return;
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
     * @return null|object
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
}
