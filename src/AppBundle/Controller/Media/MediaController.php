<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Media;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Media;
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


class MediaController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Media",
     *      description="Get the Medias",
     *      output= { "class"=Media::class, "collection"=true, "groups"={"base", "media"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "media"})
     * @Rest\Get("/medias")
     * @param Request $request
     * @return array
     */
    public function getMediasAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $medias = $em->getRepository(Media::class)->findAll();

        if (empty($medias)) {
            throw $this->getMediaNotFoundException();
        }


        return $medias;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Media",
     *      description="Get media by id",
     *      output= { "class"=Media::class, "collection"=false, "groups"={"base", "media"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "media"})
     * @Rest\Get("/medias/{mediaId}")
     * @param Request $request
     *
     * @return object
     */
    public function getMediaByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $media = $em->getRepository(Media::class)->find($request->get('mediaId'));

        if (empty($media)) {
            throw $this->getMediaNotFoundException();
        }

        return $media;
    }


    private function getMediaNotFoundException() {
        return new NotFoundHttpException("No Medias found !");
    }

}