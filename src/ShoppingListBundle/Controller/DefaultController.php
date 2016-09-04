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
        /** @var ProductService $productService */
        $productService = $this->get('hack2016.product.service');
        $productService->getReccomendedNotificationProducts();die;

        return $this->render(
            'ShoppingListBundle:Default:index.html.twig',
            ['productId' => $productId]
        );
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
    public function addProductAjaxAction(Request $request, $productId = 0)
    {
        /** @var ProductService $productService */
        $productService = $this->get('hack2016.product.service');
        try {
            $productService->saveProduct(trim($request->request->get('product', null)), $productId);
        } catch (\Exception $e){
            return new JsonResponse(array('success' => false));
        }
        return new JsonResponse(array('success' => true));
    }
}
