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

    /** @const string */
    const ID = 'hack2016.notification.service';

    /** @var  ProductService $productService */
    protected $productService;

    /** @var  EntityManager $entityManager */
    protected $entityManager;

    /**
     * @param ProductService $productService
     */
    public function setProductService($productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param OutputInterface $output
     */
    public function sendNotifications(OutputInterface $output)
    {
        $productIds = $this->productService->getRecommendedNotificationProducts();
        foreach ($productIds as $productId) {
            /** @var Products $product */
            $product = $this->entityManager->getRepository('ShoppingListBundle:Products')->find($productId);
            $output->write('Send notification for product: ' . $product->getName());
        }
    }
}