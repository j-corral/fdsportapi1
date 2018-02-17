<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Ticket;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Ticket;
use AppBundle\Repository\TicketRepository;
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
     *      output= { "class"=Ticket::class, "collection"=true, "groups"={"base", "ticket"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "ticket"})
     * @Rest\Get("/tickets")
     * @param Request $request
     * @return array
     */
    public function getTicketsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $tickets = $em->getRepository(Ticket::class)->findAvailable();

        if (empty($tickets)) {
            throw $this->getTicketNotFoundException();
        }


        return $tickets;
    }

    /**
     * @ApiDoc(
     *      resource=true, section="Ticket",
     *      description="Get shortly Tickets",
     *      output= { "class"=Ticket::class, "collection"=true, "groups"={"base", "ticket"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "ticket"})
     * @Rest\Get("/tickets/shortly/{limit}")
     * @param Request $request
     * @return array
     */
    public function getLatestTicketsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $limit = $request->get('limit');

        $tickets = $em->getRepository(Ticket::class)->findAvailable($limit);

        if (empty($tickets)) {
            throw $this->getTicketNotFoundException();
        }


        return $tickets;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Ticket",
     *      description="Get ticket by id",
     *      output= { "class"=Ticket::class, "collection"=false, "groups"={"base", "ticket"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "ticket"})
     * @Rest\Get("/tickets/{ticketId}")
     * @param Request $request
     *
     * @return object
     */
    public function getTicketByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $ticket = $em->getRepository(Ticket::class)->find($request->get('ticketId'));

        if (empty($ticket)) {
            throw $this->getTicketNotFoundException();
        }

        return $ticket;
    }


    private function getTicketNotFoundException() {
        return new NotFoundHttpException("No Tickets found !");
    }

}