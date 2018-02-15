<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Click;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Click;
use AppBundle\Entity\Product;
use AppBundle\Entity\Ticket;
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


class ClickController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="Click",
     *      description="Get the Clicks",
     *      output= { "class"=Click::class, "collection"=false, "groups"={"base", "click"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "click"})
     * @Rest\Get("/clicks")
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


    /**
     * @ApiDoc(
     *      resource=true, section="Click",
     *      description="Get click by id",
     *      output= { "class"=Click::class, "collection"=false, "groups"={"base", "click"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "click"})
     * @Rest\Get("/clicks/{clickId}")
     * @param Request $request
     *
     * @return object
     */
    public function getClickByIdAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $click = $em->getRepository(Click::class)->find($request->get('clickId'));

        if (empty($click)) {
            throw $this->getClickNotFoundException();
        }

        return $click;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Click",
     *      description="Update User and product Axe - Add new entry into clicks",
     *      output= { "class"=User::class, "collection"=false, "groups"={"base", "user"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "user"})
     * @Rest\Put("/clicks/product/{productId}")
     * @param Request $request
     *
     * @return object
     * @throws \Exception
     */
    public function updateUserProductAxeAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();

        if(empty($data) || !isset($data['cookie']) || !isset($data['cookie']['name']) || empty($data['cookie']['name'])) {
            throw new \Exception('Cookie name is empty !');
        }

        $cookieName = $data['cookie']['name'];

        // Get user
        $user = $em->getRepository(User::class)->findOneBy(["firstname" => 'user_' . $cookieName]);

        if(empty($user)) {
            throw new \Exception('User not found !');
        }


        $userAxe = $user->getAxe();

        // todo : calculate user axe


        // Get product
        $product = $em->getRepository(Product::class)->find($request->get('productId'));

        if(empty($product)) {
            throw new \Exception('Product not found !');
        }

        $productAxe = $product->getAxe();

        // todo : calculate product axe

        // update user Axe
        $user->setAxe($userAxe);

        // update product Axe
        $product->setAxe($productAxe);

        // save product and user in db
        $em->persist($user);
        $em->persist($product);
        $em->flush();

        // create click entry
        $this->addProductClick($user, $product);

        return $user;
    }


    /**
     * @ApiDoc(
     *      resource=true, section="Click",
     *      description="Update User and Ticket Axe - Add new entry into clicks",
     *      output= { "class"=User::class, "collection"=false, "groups"={"base", "user"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "user"})
     * @Rest\Put("/clicks/ticket/{ticketId}")
     * @param Request $request
     *
     * @return object
     * @throws \Exception
     */
    public function updateUserTicketAxeAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();

        if(empty($data) || !isset($data['cookie']) || !isset($data['cookie']['name']) || empty($data['cookie']['name'])) {
            throw new \Exception('Cookie name is empty !');
        }

        $cookieName = $data['cookie']['name'];

        // Get user
        $user = $em->getRepository(User::class)->findOneBy(["firstname" => 'user_' . $cookieName]);

        if(empty($user)) {
            throw new \Exception('User not found !');
        }


        $userAxe = $user->getAxe();

        // todo : calculate user axe


        // Get ticket
        $ticket = $em->getRepository(Ticket::class)->find($request->get('ticketId'));

        if(empty($ticket)) {
            throw new \Exception('Ticket not found !');
        }

        $ticketAxe = $ticket->getAxe();

        // todo : calculate ticket axe

        // update user Axe
        $user->setAxe($userAxe);

        // update product Axe
        $ticket->setAxe($ticketAxe);

        // save ticket and user in db
        $em->persist($user);
        $em->persist($ticket);
        $em->flush();

        // create click entry
        $this->addTicketClick($user, $ticket);

        return $user;
    }


    /**
     * @param $user
     * @param $product
     */
    private function addProductClick($user, $product) {
        $em = $this->getDoctrine()->getManager();
        $click = new Click();
        $click->setUser($user);
        $click->setProduct($product);
        $em->persist($click);
        $em->flush();
    }


    /**
     * @param $user
     * @param $ticket
     */
    private function addTicketClick($user, $ticket) {
        $em = $this->getDoctrine()->getManager();
        $click = new Click();
        $click->setUser($user);
        $click->setTicket($ticket);
        $em->persist($click);
        $em->flush();
    }


    /**
     * @return NotFoundHttpException
     */
    private function getClickNotFoundException() {
        return new NotFoundHttpException("No Clicks found !");
    }

}