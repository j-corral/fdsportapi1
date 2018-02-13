<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Cookie;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Cookie;
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


class CookieController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Cookie",
     *      description="Get the Cookies",
     *      output= { "class"=Cookie::class, "collection"=false, "groups"={"base", "cookie"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "cookie"})
     * @Rest\Get("/cookies")
     * @param Request $request
     * @return array
     */
    public function getCookiesAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $cookies = $em->getRepository(Cookie::class)->findAll();

        if (empty($cookies)) {
            throw $this->getCookieNotFoundException();
        }


        return $cookies;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Cookie",
     *      description="Get cookie by id",
     *      output= { "class"=Cookie::class, "collection"=false, "groups"={"base", "cookie"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "cookie"})
     * @Rest\Get("/cookies/{cookieId}")
     * @param Request $request
     *
     * @return object
     */
    public function getCookieByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $cookie = $em->getRepository(Cookie::class)->find($request->get('cookieId'));

        if (empty($cookie)) {
            throw $this->getCookieNotFoundException();
        }

        return $cookie;
    }


    private function getCookieNotFoundException() {
        return new NotFoundHttpException("No Cookies found !");
    }

}