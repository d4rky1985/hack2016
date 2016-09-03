<?php

namespace UserBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Facebook\Authentication\AccessToken;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

class NotifyService
{
    /** @const string */
    const ID = 'notify.service';

    /** @var  EntityManager */
    protected $entityManager;

    /** @var  UserService */
    protected $userService;

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
            if ($user->getId() == 1) {
                continue;
            }
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
                                $this->notifyUser($result);
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
}