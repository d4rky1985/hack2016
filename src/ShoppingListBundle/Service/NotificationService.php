<?php
namespace ShoppingListBundle\Service;

use ShoppingListBundle\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;
use UserBundle\Service\NotifyService;

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

    /** @var  NotifyService */
    protected $notifyService;

    protected $appLink;

    /** @var Router */
    protected $router;

    /**
     * @param mixed $appLink
     */
    public function setAppLink($appLink)
    {
        $this->appLink = $appLink;
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

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

    /**
     * @param OutputInterface $output
     */
    public function sendNotifications(OutputInterface $output)
    {
        $productIds = $this->productService->getRecommendedNotificationProducts();
        $this->router->getContext()->setBaseUrl($this->appLink);

        foreach ($productIds as $productId) {
            /** @var Products $product */
            $product = $this->entityManager->getRepository('ShoppingListBundle:Products')->find($productId);
            $this->getNotifyService()->sendPushNotification(
                $this->getUser()->getPushToken(),
                $product,
                'You may need to buy ' . $product->getName() . '!',
                $this->router->generate('shopping_list_notification_default', array('productId' => $product->getId()))
            );

            $output->write('Send notification for product: ' . $product->getName());
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->entityManager->getRepository('AppBundle:User')->find(1);
    }
}