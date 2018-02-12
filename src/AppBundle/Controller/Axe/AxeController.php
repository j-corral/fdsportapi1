<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Axe;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Axe;
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


class AxeController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Axe",
     *      description="Get the axes",
     *      output= { "class"=Axe::class, "collection"=true, "groups"={"base"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "axe"})
     * @Rest\Get("/axes")
     * @param Request $request
     * @return array
     */
    public function getAxesAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $axes = $em->getRepository(Axe::class)->findAll();

        if (empty($axes)) {
            throw $this->getAxeNotFoundException();
        }


        return $axes;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Axe",
     *      description="Get axe by id",
     *      output= { "class"=Axe::class, "collection"=false, "groups"={"base", "axe"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "axe"})
     * @Rest\Get("/axes/{axeId}")
     * @param Request $request
     *
     * @return object
     */
    public function getAxeByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $axe = $em->getRepository(Axe::class)->find($request->get('axeId'));

        if (empty($axe)) {
            throw $this->getAxeNotFoundException();
        }


        return $axe;
    }

    private function getAxeNotFoundException() {
        return new NotFoundHttpException("No axes found !");
    }

}