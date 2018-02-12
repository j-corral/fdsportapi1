<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Ticket;

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

class TicketController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Ticket",
     *      description="Get the Tickets",
     *      output= { "class"=Ticket::class, "collection"=false, "groups"={"base"} }
     * )
     *
     * @Rest\View(serializerGroups={"base"})
     * @Rest\Get("/tickets/{TicketId}")
     * @param Request $request
     * @return array
     */
    public function getTicketsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $tickets = $em->getRepository(Ticket::class)->findAll();

        if (empty($tickets)) {
            throw $this->getTicketNotFoundException();
        }


        return $tickets;
    }


    private function getTicketNotFoundException() {
        return new NotFoundHttpException("No Tickets found !");
    }

}