<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Sport;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Sport;
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


class SportController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Sport",
     *      description="Get the Sports",
     *      output= { "class"=Sport::class, "collection"=false, "groups"={"base", "sport"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "sport"})
     * @Rest\Get("/sports")
     * @param Request $request
     * @return array
     */
    public function getSportsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $sports = $em->getRepository(Sport::class)->findAll();

        if (empty($sports)) {
            throw $this->getSportNotFoundException();
        }


        return $sports;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Sport",
     *      description="Get sport by id",
     *      output= { "class"=Sport::class, "collection"=false, "groups"={"base", "sport"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "sport"})
     * @Rest\Get("/sports/{sportId}")
     * @param Request $request
     *
     * @return object
     */
    public function getSportByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $sport = $em->getRepository(Sport::class)->find($request->get('sportId'));

        if (empty($sport)) {
            throw $this->getSportNotFoundException();
        }

        return $sport;
    }


    private function getSportNotFoundException() {
        return new NotFoundHttpException("No Sports found !");
    }

}