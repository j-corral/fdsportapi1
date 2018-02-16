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

        // Get product
        $product = $em->getRepository(Product::class)->find($request->get('productId'));

        if(empty($product)) {
            throw new \Exception('Product not found !');
        }

        $productAxe = $product->getAxe();

        // calculate axes
        if($product->isFixed) {
            if($userAxe->male == 0 && $userAxe->female == 0 && $userAxe->age == 0) { // if userAxe is not set
                $userAxe = $productAxe; // userAxe take productAxe properties
            }
            else {
                $userAxe->male = ($userAxe->male + $productAxe->male) / 2;
                $userAxe->female = ($userAxe->female + $productAxe->female) / 2;
                $userAxe->age = ($userAxe->age + $productAxe->age) / 2;
                $userAxe->csp = ($userAxe->csp + $productAxe->csp) / 2;

                //TODO: assign value to userAxe with means in clicks
                if($productAxe->brand != null)
                    $userAxe->brand = $productAxe->brand;
                if($productAxe->city != null)
                    $userAxe->city = $productAxe->city;
                if($productAxe->sport != null)
                    $userAxe->sport = $productAxe->sport;
            }
        }
        else if($userAxe->male != 0 && $userAxe->female != 0 && $userAxe->age != 0) { // if userAxe is set and product is floating

            if($productAxe->male == 0 && $productAxe->female == 0 && $productAxe->age == 0) { // if product axe is not set
                $productAxe = $userAxe; // productAxe take userAxe properties
            }
            else {
                $productAxe->male = ($productAxe->male + $userAxe->male) / 2;
                $productAxe->female = ($productAxe->female + $userAxe->female) / 2;
                $productAxe->age = ($productAxe->age + $userAxe->age) / 2;
                $productAxe->csp = ($productAxe->csp + $userAxe->csp) / 2;

                //TODO: assign value to productAxe with means in clicks
                if($userAxe->brand != null)
                    $productAxe->brand = $userAxe->brand;
                if($userAxe->city != null)
                    $productAxe->city = $userAxe->city;
                if($userAxe->sport != null)
                    $productAxe->sport = $userAxe->sport;
            }
        }

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

        // Get ticket
        $ticket = $em->getRepository(Ticket::class)->find($request->get('ticketId'));

        if(empty($ticket)) {
            throw new \Exception('Ticket not found !');
        }

        $ticketAxe = $ticket->getAxe();

        // calculate axes
        if($ticket->isFixed) {
            if($userAxe->male == 0 && $userAxe->female == 0 && $userAxe->age == 0) { // if userAxe is not set
                $userAxe = $ticketAxe; // userAxe take $ticketAxe properties
            }
            else {
                $userAxe->male = ($userAxe->male + $ticketAxe->male) / 2;
                $userAxe->female = ($userAxe->female + $ticketAxe->female) / 2;
                $userAxe->age = ($userAxe->age + $ticketAxe->age) / 2;
                $userAxe->csp = ($userAxe->csp + $ticketAxe->csp) / 2;

                //TODO: assign value to userAxe with means in clicks
                if($ticketAxe->brand != null)
                    $userAxe->brand = $ticketAxe->brand;
                if($ticketAxe->city != null)
                    $userAxe->city = $ticketAxe->city;
                if($ticketAxe->sport != null)
                    $userAxe->sport = $ticketAxe->sport;
            }
        }
        else if($userAxe->male != 0 && $userAxe->female != 0 && $userAxe->age != 0) { // if userAxe is set and ticket is floating

            if($ticketAxe->male == 0 && $ticketAxe->female == 0 && $ticketAxe->age == 0) { // if ticket axe is not set
                $ticketAxe = $userAxe; // $ticketAxe take userAxe properties
            }
            else {
                $ticketAxe->male = ($ticketAxe->male + $userAxe->male) / 2;
                $ticketAxe->female = ($ticketAxe->female + $userAxe->female) / 2;
                $ticketAxe->age = ($ticketAxe->age + $userAxe->age) / 2;
                $ticketAxe->csp = ($ticketAxe->csp + $userAxe->csp) / 2;

                //TODO: assign value to ticketAxe with means in clicks
                if($userAxe->brand != null)
                    $ticketAxe->brand = $userAxe->brand;
                if($userAxe->city != null)
                    $ticketAxe->city = $userAxe->city;
                if($userAxe->sport != null)
                    $ticketAxe->sport = $userAxe->sport;
            }
        }

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