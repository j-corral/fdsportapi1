<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:16
 */

namespace AppBundle\Controller\User;

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

class UserController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="User",
     *      description="Get the Users",
     *      output= { "class"=User::class, "collection"=false, "groups"={"base"} }
     * )
     *
     * @Rest\View(serializerGroups={"base"})
     * @Rest\Get("/users/{UserId}")
     * @param Request $request
     * @return array
     */
    public function getUsersAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();

        if (empty($users)) {
            throw $this->getUserNotFoundException();
        }


        return $users;
    }


    private function getUserNotFoundException() {
        return new NotFoundHttpException("No users found !");
    }

}