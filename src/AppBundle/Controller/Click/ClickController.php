<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Click;

// Required dependencies for Controller and Annotations
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


class ClickController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Click",
     *      description="Get the Clicks",
     *      output= { "class"=Click::class, "collection"=false, "groups"={"base"} }
     * )
     *
     * @Rest\View(serializerGroups={"base"})
     * @Rest\Get("/clicks/{ClickId}")
     * @param Request $request
     * @return array
     */
    public function getClicksAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $clicks = $em->getRepository(Click::class)->findAll();

        if (empty($clicks)) {
            throw $this->getClickNotFoundException();
        }


        return $clicks;
    }


    private function getClickNotFoundException() {
        return new NotFoundHttpException("No Clicks found !");
    }

}