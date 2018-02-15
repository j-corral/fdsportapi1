<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Product;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use \AppBundle\Controller\ControllerBase;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

define('SHOP_MAN', 1);
define('SHOP_WOOMAN', 2);
define('SHOP_ACCESSORIES', 3);
define('SHOP_EQUIPMENT', 4);

class ProductController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Product",
     *      description="Get the products",
     *      output= { "class"=Product::class, "collection"=true, "groups"={"base", "product"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product", "axe"})
     * @Rest\Get("/products")
     * @param Request $request
     * @return array
     */
    public function getProductsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)->findAll();

        if (empty($products)) {
            throw $this->getProductNotFoundException();
        }


        return $products;
    }



    /**
     * @ApiDoc(
     *      resource=true, section="Product",
     *      description="Get the products for man",
     *      output= { "class"=Product::class, "collection"=true, "groups"={"base", "product"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product", "axe"})
     * @Rest\Get("/products/man")
     * @param Request $request
     * @return array
     */
    public function getProductsManAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)->findBy(['type' => SHOP_MAN]);

        if (empty($products)) {
            throw $this->getProductNotFoundException();
        }


        return $products;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Product",
     *      description="Get the products for wooman",
     *      output= { "class"=Product::class, "collection"=true, "groups"={"base", "product"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product", "axe"})
     * @Rest\Get("/products/wooman")
     * @param Request $request
     * @return array
     */
    public function getProductsWoomanAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)->findBy(['type' => SHOP_WOOMAN]);

        if (empty($products)) {
            throw $this->getProductNotFoundException();
        }


        return $products;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Product",
     *      description="Get the products accessories",
     *      output= { "class"=Product::class, "collection"=true, "groups"={"base", "product"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product", "axe"})
     * @Rest\Get("/products/accessories")
     * @param Request $request
     * @return array
     */
    public function getProductsAccessoriesAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)->findBy(['type' => SHOP_ACCESSORIES]);

        if (empty($products)) {
            throw $this->getProductNotFoundException();
        }


        return $products;
    }

    /**
     * @ApiDoc(
     *      resource=true, section="Product",
     *      description="Get the products equipment",
     *      output= { "class"=Product::class, "collection"=true, "groups"={"base", "product"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product", "axe"})
     * @Rest\Get("/products/equipment")
     * @param Request $request
     * @return array
     */
    public function getProductsEquipmentAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)->findBy(['type' => SHOP_EQUIPMENT]);

        if (empty($products)) {
            throw $this->getProductNotFoundException();
        }


        return $products;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Product",
     *      description="Get product by id",
     *      output= { "class"=Product::class, "collection"=false, "groups"={"base", "product"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product"})
     * @Rest\Get("/products/{productId}")
     * @param Request $request
     *
     * @return object
     */
    public function getProductByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(Product::class)->find($request->get('productId'));

        if (empty($product)) {
            throw $this->getProductNotFoundException();
        }

        return $product;
    }


    private function getProductNotFoundException() {
        return new NotFoundHttpException("Product not found !");
    }

}