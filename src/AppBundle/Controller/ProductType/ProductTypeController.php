<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\ProductType;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\ProductType;
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


class ProductTypeController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="ProductType",
     *      description="Get the ProductTypes",
     *      output= { "class"=ProductType::class, "collection"=false, "groups"={"base", "product_type"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product_type"})
     * @Rest\Get("/product_types")
     * @param Request $request
     * @return array
     */
    public function getProductTypesAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $product_types = $em->getRepository(ProductType::class)->findAll();

        if (empty($product_types)) {
            throw $this->getProductTypeNotFoundException();
        }


        return $product_types;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="ProductType",
     *      description="Get product_type by id",
     *      output= { "class"=ProductType::class, "collection"=false, "groups"={"base", "product_type"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "product_type"})
     * @Rest\Get("/product_types/{product_typeId}")
     * @param Request $request
     *
     * @return object
     */
    public function getProductTypeByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $product_type = $em->getRepository(ProductType::class)->find($request->get('product_typeId'));

        if (empty($product_type)) {
            throw $this->getProductTypeNotFoundException();
        }

        return $product_type;
    }


    private function getProductTypeNotFoundException() {
        return new NotFoundHttpException("No ProductTypes found !");
    }

}