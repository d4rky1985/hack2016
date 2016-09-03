<?php

namespace UserBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Facebook\Facebook;

class UserService
{
    /** @const string */
    const ID = 'user.service';

    /** @var  EntityManager */
    protected $entityManager;

    /**
     * @return array
     */
    public function getFacebookHelper()
    {
        $fb = new Facebook([
            'app_id' => '334405686892982',
            'app_secret' => '3cffe9f90f909e68101f7a95e5170a6f',
            'default_graph_version' => 'v2.7',
        ]);

        return array(
            $fb,
            $fb->getRedirectLoginHelper()
        );
    }

    /**
     * @param $token
     * @param $fbUser
     *
     * @return User
     */
    public function saveUser($token, $fbUser) : User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository('AppBundle:User');

        $user = $userRepository->findOneBy(
            array(
                'fbId' => $fbUser['id']
            )
        );

        if (!$user instanceof User) {
            $user = new User();

            $user->setSex($fbUser['gender']);
        }

        $user->setFbId($fbUser['id']);
        $user->setFbToken($token);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }
}