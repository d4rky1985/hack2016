<?php
namespace ShoppingListBundle\Service;

use AppBundle\Entity\User;
use ShoppingListBundle\Entity\Products;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use UserBundle\Service\NotifyService;

/**
 * Class NotificationService.
 */
class NotificationService
{
    /** @var  ProductService */
    protected $productService;

    /** @var  EntityManager */
    protected $entityManager;

    /** @var  NotifyService */
    protected $notifyService;

    protected $appLink;

    /** @var Router */
    protected $router;

    /**
     * @return mixed
     */
    public function getAppLink()
    {
        return $this->appLink;
    }

    /**
     * @param mixed $appLink
     */
    public function setAppLink($appLink)
    {
        $this->appLink = $appLink;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

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

    /**
     * @return NotifyService
     */
    public function getNotifyService()
    {
        return $this->notifyService;
    }

    /**
     * @param NotifyService $notifyService
     */
    public function setNotifyService($notifyService)
    {
        $this->notifyService = $notifyService;
    }

    public function sendNotifications(OutputInterface $output)
    {
        $productIds = $this->getProductService()->getReccomendedNotificationProducts();
        $this->getRouter()->getContext()->setBaseUrl($this->getAppLink());

        foreach ($productIds as $productId) {
            /** @var Products $product */
            $product = $this->getEntityManager()->getRepository('ShoppingListBundle:Products')->find($productId);
           $this->getNotifyService()->sendPushNotification(
                $this->getUser()->getPushToken(),
                $product,
                'You may need to buy ' . $product->getName() . '!',
                $this->getRouter()->generate('shopping_list_notification_default', array('productId' => $product->getId()))
            );

            $output->write('Send notification for product: ' . $product->getName());
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->getEntityManager()->getRepository('AppBundle:User')->find(1);
    }
}