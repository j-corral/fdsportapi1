<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Brand;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Brand;
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


class BrandController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Brand",
     *      description="Get the Brands",
     *      output= { "class"=Brand::class, "collection"=true, "groups"={"base", "brand"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "brand"})
     * @Rest\Get("/brands")
     * @param Request $request
     * @return array
     */
    public function getBrandsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $brands = $em->getRepository(Brand::class)->findAll();

        if (empty($brands)) {
            throw $this->getBrandNotFoundException();
        }


        return $brands;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Brand",
     *      description="Get brand by id",
     *      output= { "class"=Brand::class, "collection"=false, "groups"={"base", "brand"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "brand"})
     * @Rest\Get("/brands/{brandId}")
     * @param Request $request
     *
     * @return object
     */
    public function getBrandByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($request->get('brandId'));

        if (empty($brand)) {
            throw $this->getBrandNotFoundException();
        }

        return $brand;
    }


    private function getBrandNotFoundException() {
        return new NotFoundHttpException("No Brands found !");
    }

}