<?php

namespace ShoppingListBundle\Controller;

use ShoppingListBundle\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    const NO_PRODUCT_ID = 0;

    public function indexAction($productId = self::NO_PRODUCT_ID)
    {
<<<<<<< HEAD
        return $this->render(
            'ShoppingListBundle:Default:index.html.twig',
            ['productId' => $productId]
        );
=======
        /** @var ProductService $productService */
        $productService = $this->get(ProductService::ID);

        $productList = $productService->getShoppingListProducts();

        return $this->render('ShoppingListBundle:Default:index.html.twig');
>>>>>>> 0dec911fa0a090860be489a2334f1e2b745fecf4
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

    /**\
     * @param Request $request
     * @param $productId
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
