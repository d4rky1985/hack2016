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
            'app_id' => '1092426070826105',
            'app_secret' => '0b4ca07f6589f642575ee0d8e152a09f',
            'default_graph_version' => 'v2.5',
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