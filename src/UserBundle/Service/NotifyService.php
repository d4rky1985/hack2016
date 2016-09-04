<?php

namespace UserBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Facebook\Authentication\AccessToken;
use ShoppingListBundle\Entity\ProductsSuggestions;
use ShoppingListBundle\Repository\ProductsSuggestionsRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\FeelingMapping;
use UserBundle\Repository\FeelingMappingRepository;

/**
 * Class NotifyService
 *
 * @package UserBundle\Service
 */
class NotifyService
{
    /** @const string */
    const ID = 'notify.service';

    /** @var  EntityManager */
    protected $entityManager;

    /** @var  UserService */
    protected $userService;

    /** @var  string */
    protected $pushNotificationToken;

    /** @var  string */
    protected $pushNotificationServer;

    /** @var Router */
    protected $router;

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
     * @return mixed
     */
    public function getPushNotificationToken()
    {
        return $this->pushNotificationToken;
    }

    /**
     * @param mixed $pushNotificationToken
     */
    public function setPushNotificationToken($pushNotificationToken)
    {
        $this->pushNotificationToken = $pushNotificationToken;
    }

    /**
     * @return string
     */
    public function getPushNotificationServer()
    {
        return $this->pushNotificationServer;
    }

    /**
     * @param string $pushNotificationServer
     */
    public function setPushNotificationServer($pushNotificationServer)
    {
        $this->pushNotificationServer = $pushNotificationServer;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function run(InputInterface $input, OutputInterface $output) : bool
    {
        $this->input = $input;
        $this->output = $output;

        try {
            $this->executeCronJob($input, $output);
        } catch (\Exception $e) {
            $this->output->writeln(
                '<info>[' . date('Y-m-d H:i:s') . ']</info> > ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString()
            );

            return false;
        }

        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function executeCronJob(InputInterface $input, OutputInterface $output)
    {
        // process action option
        $method = $input->getOption('action') . 'CronAction';

        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException("Method {$method} is not defined in " . get_class($this));
        }

        return $this->$method($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function sendNotificationsCronAction(InputInterface $input, OutputInterface $output)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository('AppBundle:User');

        $usersToCheck = $userRepository->getUsersToNotify();

        $output->writeln('<info>[' . date('Y-m-d H:i:s') . ']' .
            'Found ' . count($usersToCheck) . ' to send notifications!</info>');

        list ($fb, $fbHelper) = $this->userService->getFacebookHelper();

        /** @var User $user */
        foreach ($usersToCheck as $user) {
            $accesToken = new AccessToken($user->getFbToken());

            try {
                $response = $fb->get('/me/feed?limit=10', $accesToken);

                if ($response->getHttpStatusCode() == Response::HTTP_OK) {
                    $fbPosts = $response->getDecodedBody();

                    foreach ($fbPosts['data'] as $post) {
                        if (isset($post['message'])) {
                            $words = explode(' ', $post['message']);
                            $result = $this->verifyFeed($words);

                            if ($result) {
                                $this->notifyUser($user, $result);
                                return;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                $output->writeln('<info>[' . date('Y-m-d H:i:s') . ']' .
                    'Error: ' . $e->getMessage() . '</info>');
            }
        }
    }

    /**
     * @param $words
     *
     * @return bool|null|FeelingMapping
     */
    public function verifyFeed($words)
    {
        /** @var FeelingMappingRepository $feelingMappingRepository */
        $feelingMappingRepository = $this->entityManager->getRepository('UserBundle:FeelingMapping');

        foreach ($words as $word) {
            $word = $this->removePunctuationFromWord($word);

            $feelingMapping = $feelingMappingRepository->findOneBy(
                array(
                    'fbStatus' => $word
                )
            );

            if ($feelingMapping instanceof FeelingMapping) {
                return $feelingMapping;
            }
        }

        return false;
    }

    /**
     * @param User $user
     * @param FeelingMapping $feelingMapping
     */
    public function notifyUser($user, $feelingMapping)
    {
        /** @var ProductsSuggestionsRepository $productSuggentionRepository */
        $productSuggentionRepository = $this->entityManager->getRepository('ShoppingListBundle:ProductsSuggestions');

        $productsSuggention = $productSuggentionRepository->findBy(
            array(
                'feelingGroup' => $feelingMapping->getStatusGroup(),
                'gender' => $user->getSex()
            )
        );
        shuffle($productsSuggention);

        $productSuggention = $productsSuggention[0];

        $this->sendPushNotification($user->getPushToken(), $productSuggention);
    }

    /**
     * @param $word
     *
     * @return string
     */
    public function removePunctuationFromWord($word) : string
    {
        $charsToRemove = array('!', ',', '.', '#', '?');

        return str_replace($charsToRemove, '', $word);
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param $userPushToken
     * @param ProductsSuggestions $product
     * @return bool
     */
    public function sendPushNotification($userPushToken, $product, $message = null, $url = null) : bool
    {
        if (is_null($message)) {
            $message = $product->getShortDescription();
        }
        if (is_null($url)) {
            $url = $this->getRouter()->generate('shopping_list_default', array('productId' => $product->getId()));
        }
        $fields = array
        (
            'token'     => $this->getPushNotificationToken(),
            'user'      => $userPushToken,
            'message'   => $message,
            'title'     => "Wish Tellers",
            'url_title' => 'View more details',
            'url'       => $url,
        );

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $this->getPushNotificationServer() );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch );
        curl_close( $ch );

        return $result['status'] === 1;
    }
}