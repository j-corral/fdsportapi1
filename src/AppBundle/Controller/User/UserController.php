<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:16
 */

namespace AppBundle\Controller\User;

// Required dependencies for Controller and Annotations
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

class UserController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="User",
     *      description="Get the Users",
     *      output= { "class"=User::class, "collection"=false, "groups"={"base", "user"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "user"})
     * @Rest\Get("/users")
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


    /**
     * @ApiDoc(
     *      resource=true, section="User",
     *      description="Get user by id",
     *      output= { "class"=User::class, "collection"=false, "groups"={"base", "user"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "user"})
     * @Rest\Get("/users/{userId}")
     * @param Request $request
     *
     * @return object
     */
    public function getUserByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($request->get('userId'));

        if (empty($user)) {
            throw $this->getUserNotFoundException();
        }

        return $user;
    }


    private function getUserNotFoundException() {
        return new NotFoundHttpException("No users found !");
    }

}