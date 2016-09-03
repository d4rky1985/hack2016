<?php

namespace UserBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Service\UserService;

/**
 * Class DefaultController
 *
 * @package UserBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        /** @var UserService $userService */
        $userService = $this->get('user.service');

        $error = '';
        $loginUrl = '';
        try {
            list ($fb, $fbHelper) = $userService->getFacebookHelper();

            $permissions = ['public_profile', 'user_friends', 'user_about_me', 'user_birthday', 'user_events', 'user_posts', 'user_relationships', 'user_relationship_details', 'read_stream'];
            $loginUrl = $fbHelper->getLoginUrl('http://hack.ion.ghitun.rtr1-dev.emag.network/app_dev.php/user/login', $permissions);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->render(
            'UserBundle:Default:index.html.twig',
            array(
                'loginUrl' => $loginUrl,
                'error' => $error
            )
        );
    }

    public function loginAction()
    {
        /** @var UserService $userService */
        $userService = $this->get('user.service');

        $error = '';
        try {
            list ($fb, $fbHelper) = $userService->getFacebookHelper();
            $accessToken = $fbHelper->getAccessToken();

            if (isset($accessToken)) {
                $token = $accessToken->getValue();
                $res = $fb->get('/me?fields=id,name,gender,age_range', $accessToken);

                if ($res->getHttpStatusCode() == Response::HTTP_OK) {
                    $fbUser = $res->getDecodedBody();

                    /** @var User $user */
                    $user = $userService->saveUser($token, $fbUser);
                }
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            var_dump($e->getMessage());die();
        }

        return $this->render(
            'UserBundle:Default:logged.html.twig',
            array(
                'error' => $error
            )
        );
    }
}
