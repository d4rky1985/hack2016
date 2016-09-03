<?php

namespace ShoppingListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ShoppingListBundle:Default:index.html.twig');
    }

    /**
     * @return JsonResponse
     */
    public function addProductAjaxAction()
    {
        return new JsonResponse(array('success' => true));
    }
}
