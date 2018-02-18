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
use AppBundle\Entity\Cookie;
use AppBundle\Entity\User;
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
     * @param $products
     * @param $user
     * @param int $product_id
     * @return mixed
     */
    private function orderProducts($products, $user, $product_id = 0) {
        //NOTE: To change the criteria of sorting, you can change the order of foreach
        $toProcess = $products; // Array of products to sort
        $processed = array(); // Array of products sorted

        if(empty($user) || empty($user->getAxe())) {
            return $products;
        }

        // Process male or female
        foreach ($toProcess as $key => $product) { // NOTE: Je n'ai pas oublié le cas où le male == female. Ces produits ne seront pas écartés par les 2 if et si les axes de l'utilisateur sont égaux, aucun des if n'écartera de produit
            $userMale = false;
            $userFemale = false;
            if($user->getAxe()->getMale() > $user->getAxe()->getFemale())
                $userMale = true;
            else if($user->getAxe()->getMale() < $user->getAxe()->getFemale())
                $userFemale = true;


            if(empty($product) || empty($product->getAxe())) {
                continue;
            }


            // remove current product from suggests
            if($product_id > 0 && $product->getProductId() == $product_id) {
                unset($toProcess[$key]);
            }


            if(($product->getAxe()->getMale() > $product->getAxe()->getFemale()) && $userFemale && !$userMale) { // If product is male and user is female
                array_unshift($processed, $product); // We add this product to processed
                unset($toProcess[$key]); // Remove the actual product
            }
            else if(($product->getAxe()->getMale() < $product->getAxe()->getFemale()) && !$userFemale && $userMale) { // If product is female and user is male
                array_unshift($processed, $product); // We add this product to processed
                unset($toProcess[$key]); // Remove the actual product
            }
        }

        // Process sports
        foreach ($toProcess as $key => $product) {

            if(empty($product) || empty($product->getAxe()) || empty($product->getAxe()->getSport())) {
                continue;
            }


            if($product->getAxe()->getSport() != $user->getAxe()->getSport()) {
                array_unshift($processed, $product); // We add this product to processed
                unset($toProcess[$key]); // Remove the actual product
            }
        }

        // Process age
        foreach ($toProcess as $key => $product) {

            if(empty($product) || empty($product->getAxe()) || empty($product->getAxe()->getAge())) {
                continue;
            }

            $ageMargin = 0.9;
            if(($product->getAxe()->getAge() >= ($user->getAxe()->getAge() + $ageMargin)) && ($product->getAxe()->getAge() <= ($user->getAxe()->getAge() - $ageMargin))) { // If product age is not in user->age-0,9 <= product->age <= user->age+0,9
                array_unshift($processed, $product); // We add this product to processed
                unset($toProcess[$key]); // Remove the actual product
            }
        }

        // Process CSP
        foreach ($toProcess as $key => $product) {
            if(empty($product) || empty($product->getAxe()) || empty($product->getAxe()->getCsp())) {
                continue;
            }

            $cspMargin = 10;
            if(($product->getAxe()->getCsp() >= ($user->getAxe()->getCsp() + $cspMargin)) && ($product->getAxe()->getCsp() <= ($user->getAxe()->getCsp() - $cspMargin))) { // If product csp is not in user->csp-10 <= product->age <= user->csp+10
                array_unshift($processed, $product); // We add this product to processed
                unset($toProcess[$key]); // Remove the actual product
            }
        }

        // Process brand
        foreach ($toProcess as $key => $product) {
            if(empty($product) || empty($product->getAxe()) || empty($product->getAxe()->getBrand())) {
                continue;
            }

            if($product->getAxe()->getBrand() != $user->getAxe()->getBrand()) {
                array_unshift($processed, $product); // We add this product to processed
                unset($toProcess[$key]); // Remove the actual product
            }
        }

        // Process city (Reserved for future use)
//        foreach ($toProcess as $key => $product) {
//            if($product->getAxe()->getCity() != $user->getAxe()->getCity()) {
//                array_unshift($processed, $product); // We add this product to processed
//                unset($toProcess[$key]); // Remove the actual product
//            }
//        }

        //$processed = $toProcess + $processed; // We add last elements in first positions
        return $toProcess; // Return ordered array
    }

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

        $products = $em->getRepository(Product::class)->findNewest();

        if (empty($products)) {
            throw $this->getProductNotFoundException();
        }


        return $products;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Product",
     *      description="Get newest products",
     *      output= { "class"=Product::class, "collection"=true, "groups"={"base", "product"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product", "axe"})
     * @Rest\Get("/products/newest/{limit}")
     * @param Request $request
     * @return array
     */
    public function getNewestProductsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $limit = $request->get('limit');

        $products = $em->getRepository(Product::class)->findNewest($limit);

        if (empty($products)) {
            throw $this->getProductNotFoundException();
        }


        return $products;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Product",
     *      description="Get suggested products",
     *      output= { "class"=Product::class, "collection"=true, "groups"={"base", "product"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product", "axe"})
     * @Rest\Get("/products/suggested/{cookie}")
     * @QueryParam(name="product_id", requirements="\d+", default="0", description="product_id")
     * @QueryParam(name="limit", requirements="\d+", default="0", description="max results")
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @return array
     */
    public function getSuggestedProductsAction(Request $request, ParamFetcher $paramFetcher) {
        $em = $this->getDoctrine()->getManager();

        $cookieName = $request->get('cookie');
        $product_id = (int) $paramFetcher->get('product_id');
        $limit = (int) $paramFetcher->get('limit');

        $products = $em->getRepository(Product::class)->findAll();

        if (empty($products)) {
            throw $this->getProductNotFoundException();
        }


        $cookie = $em->getRepository(Cookie::class)->findOneByName($cookieName);

        if(!empty($cookie)) {

            // Get user
            $user = $em->getRepository(User::class)->findOneByCookie($cookie);

            if(!empty($user)) {
                $products = $this->orderProducts($products, $user, $product_id);
            }

        }


        if($limit && count($products) > $limit) {
            $products = array_slice($products, 0, $limit);
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

        $products = $em->getRepository(Product::class)->findBy([
            'type' => SHOP_MAN
        ], [
            'product_id' => "DESC"
        ]);

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

        $products = $em->getRepository(Product::class)->findBy([
            'type' => SHOP_WOOMAN
        ], [
            'product_id' => "DESC"
        ]);

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

        $products = $em->getRepository(Product::class)->findBy([
            'type' => SHOP_ACCESSORIES
        ], [
            'product_id' => "DESC"
        ]);

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

        $products = $em->getRepository(Product::class)->findBy([
            'type' => SHOP_EQUIPMENT
        ], [
            'product_id' => "DESC"
        ]);

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