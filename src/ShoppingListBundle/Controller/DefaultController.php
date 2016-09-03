<?php

namespace ShoppingListBundle\Controller;

use ShoppingListBundle\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction()
    {
        /** @var ProductService $productService */
        $productService = $this->get(ProductService::ID);

        $productList = $productService->getShoppingListProducts();

        return $this->render('ShoppingListBundle:Default:index.html.twig');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchProductsAction(Request $request)
    {
        $products = $this->getDoctrine()
            ->getRepository('ShoppingListBundle:Products')
            ->findByNameLike($request->request->get('product'));

        return new JsonResponse($products);
    }

    /**
     * @return JsonResponse
     */
    public function addProductAjaxAction(Request $request)
    {
        /** @var ProductService $productService */
        $productService = $this->get('hack2016.product.service');
        try {
            $productService->saveProduct(trim($request->request->get('product')));
        } catch (\Exception $e){
            print_r($e->getMessage());die;
            return new JsonResponse(array('success' => false));
        }
        return new JsonResponse(array('success' => true));
    }
}
