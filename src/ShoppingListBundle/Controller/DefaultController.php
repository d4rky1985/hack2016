<?php

namespace ShoppingListBundle\Controller;

use ShoppingListBundle\Entity\Products;
use ShoppingListBundle\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    const NO_PRODUCT_ID = 0;

    public function indexAction($productId = self::NO_PRODUCT_ID)
    {
        /** @var ProductService $productService */
        $productService = $this->get(ProductService::ID);

        $productsList = $productService->getShoppingListProducts();

        $options = ['productsList' => $productsList];

        if (!empty($productId)) {
            try {
                $options['suggestedProduct'] = $productService->getRecommendedProduct($productId);
            } catch (NotFoundHttpException $e) {
                error_log('Invalid product id ' . $productId);
            }
        }

        return $this->render('ShoppingListBundle:Default:index.html.twig', $options);
    }


    public function addFromNotificationAction($productId = self::NO_PRODUCT_ID)
    {
        /** @var Products $product */
        $product = $this->getDoctrine()->getRepository('ShoppingListBundle:Products')->find($productId);
        /** @var ProductService $productService */
        $productService = $this->get(ProductService::ID);
        $productService->saveProduct($product->getName());
        $productsList = $productService->getShoppingListProducts();
        $options = ['productsList' => $productsList];
        return $this->render('ShoppingListBundle:Default:index.html.twig', $options);
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
        $productService = $this->get(ProductService::ID);
        try {
            $productService->saveProduct(trim($request->request->get('product', null)), $productId);
        } catch (\Exception $e){
            return new JsonResponse(array('success' => false, 'message' => $e->getMessage()));
        }
        return new JsonResponse(array('success' => true));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function productBoughtAjaxAction(Request $request) {
        /** @var ProductService $productService */
        $productService = $this->get(ProductService::ID);
        try {
            $productService->productIsBought(trim($request->request->get('product', null)));
        } catch (\Exception $e){
            return new JsonResponse(array('success' => false));
        }
        return new JsonResponse(array('success' => true));
    }
}
