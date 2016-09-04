<?php
namespace ShoppingListBundle\Service;

use ShoppingListBundle\Entity\Products;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class NotificationService.
 */
class NotificationService
{
    /** @var  ProductService */
    protected $productService;

    /** @var  EntityManager */
    protected $entityManager;

    /**
     * @return ProductService
     */
    public function getProductService()
    {
        return $this->productService;
    }

    /**
     * @param ProductService $productService
     */
    public function setProductService($productService)
    {
        $this->productService = $productService;
    }

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

    public function sendNotifications(OutputInterface $output)
    {
        $productIds = $this->getProductService()->getReccomendedNotificationProducts();
        foreach ($productIds as $productId) {
            /** @var Products $product */
            $product = $this->getEntityManager()->getRepository('ShoppingListBundle:Products')->find($productId);
            $output->write('Send notification for product: ' . $product->getName());
        }
    }
}