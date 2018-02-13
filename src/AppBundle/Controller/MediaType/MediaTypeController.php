<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\MediaType;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\MediaType;
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


class MediaTypeController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="MediaType",
     *      description="Get the MediaTypes",
     *      output= { "class"=MediaType::class, "collection"=false, "groups"={"base", "media_type"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "media_type"})
     * @Rest\Get("/media_types")
     * @param Request $request
     * @return array
     */
    public function getMediaTypesAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $media_types = $em->getRepository(MediaType::class)->findAll();

        if (empty($media_types)) {
            throw $this->getMediaTypeNotFoundException();
        }


        return $media_types;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="MediaType",
     *      description="Get media_type by id",
     *      output= { "class"=MediaType::class, "collection"=false, "groups"={"base", "media_type"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "media_type"})
     * @Rest\Get("/media_types/{media_typeId}")
     * @param Request $request
     *
     * @return object
     */
    public function getMediaTypeByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $media_type = $em->getRepository(MediaType::class)->find($request->get('media_typeId'));

        if (empty($media_type)) {
            throw $this->getMediaTypeNotFoundException();
        }

        return $media_type;
    }


    private function getMediaTypeNotFoundException() {
        return new NotFoundHttpException("No MediaTypes found !");
    }

}