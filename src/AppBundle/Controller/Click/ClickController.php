<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/02/18
 * Time: 17:15
 */

namespace AppBundle\Controller\Click;

// Required dependencies for Controller and Annotations
use AppBundle\Entity\Axe;
use AppBundle\Entity\Click;
use AppBundle\Entity\Cookie;
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
     *      description="Get click by user cookie name",
     *      output= { "class"=Click::class, "collection"=false, "groups"={"base", "click"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "click"})
     * @Rest\Get("/clicks/user/{cookieName}")
     * @param Request $request
     *
     * @return object
     * @throws \Exception
     */
    public function getClickByUserAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $cookieName = $request->get('cookieName');

        $cookie = $em->getRepository(Cookie::class)->findOneByName($cookieName);

        if(empty($cookie)) {
            throw new \Exception('Cookie not found !');
        }

        // Get user
        $user = $em->getRepository(User::class)->findOneByCookie($cookie);

        if(empty($user)) {
            throw new \Exception('User not found !');
        }

        $click = $em->getRepository(Click::class)->findBy([
            "user" => $user
        ], ["click_id" => "DESC"]);

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

        $cookie = $em->getRepository(Cookie::class)->findOneByName($cookieName);

        if(empty($cookie)) {
            throw new \Exception('Cookie not found !');
        }

        // Get user
        $user = $em->getRepository(User::class)->findOneByCookie($cookie);


        if(empty($user)) {
            throw new \Exception('User not found !');
        }

        $userAxe = $user->getAxe();

        if(is_null($userAxe)) {
            throw new \Exception('User Axe does not exist !');
        }

        // Get product
        $product = $em->getRepository(Product::class)->find($request->get('productId'));

        if(empty($product)) {
            throw new \Exception('Product not found !');
        }

        $productAxe = $product->getAxe();

        if(is_null($productAxe)) {
            // copy user Axe into product Axe
            $productAxe = new Axe();
            $productAxe->setAge($userAxe->getAge());
            $productAxe->setBrand($userAxe->getBrand());
            $productAxe->setCity($userAxe->getCity());
            $productAxe->setCsp($userAxe->getCsp());
            $productAxe->setFemale($userAxe->getFemale());
            $productAxe->setMale($userAxe->getMale());
            $productAxe->setSport($userAxe->getSport());
            $em->persist($productAxe);
            $product->setAxe($productAxe);
            $em->merge($product);
            $em->flush();
            return $user;
        }

        // calculate axes
        if($product->getisFixed()) {
            if($userAxe->getMale() == 0 && $userAxe->getFemale() == 0 && $userAxe->getAge() == 0) { // if userAxe is not set
                $userAxe = $productAxe; // userAxe take productAxe properties
            }
            else {
                $userAxe->setMale(($userAxe->getMale() + $productAxe->getMale()) / 2);
                $userAxe->setFemale(($userAxe->getFemale() + $productAxe->getFemale()) / 2);
                $userAxe->setAge(($userAxe->getAge() + $productAxe->getAge()) / 2);
                $userAxe->setCsp(($userAxe->getCsp() + $productAxe->getCsp()) / 2);

                //TODO: assign value to userAxe with means in clicks
                if($productAxe->getBrand() != null)
                    $userAxe->setBrand($productAxe->getBrand());
                if($productAxe->getCity() != null)
                    $userAxe->setCity($productAxe->getCity());
                if($productAxe->getSport() != null)
                    $userAxe->setSport($productAxe->getSport());
            }
        }
        else if($userAxe->getMale() != 0 && $userAxe->getFemale() != 0 && $userAxe->getAge() != 0) { // if userAxe is set and product is floating

            if($productAxe->getMale() == 0 && $productAxe->getFemale() == 0 && $productAxe->getAge() == 0) { // if product axe is not set
                $productAxe = $userAxe; // productAxe take userAxe properties
            }
            else {
                $productAxe->setMale(($productAxe->getMale() + $userAxe->getMale()) / 2);
                $productAxe->setFemale(($productAxe->getFemale() + $userAxe->getFemale()) / 2);
                $productAxe->setAge(($productAxe->getAge() + $userAxe->getAge()) / 2);
                $productAxe->setCsp(($productAxe->getCsp() + $userAxe->getCsp()) / 2);

                //TODO: assign value to productAxe with means in clicks
                if($userAxe->getBrand() != null)
                    $productAxe->setBrand($userAxe->getBrand());
                if($userAxe->getCity() != null)
                    $productAxe->setCity($userAxe->getCity());
                if($userAxe->getSport() != null)
                    $productAxe->setSport($userAxe->getSport());
            }
        }

        // update user Axe
        $user->setAxe($userAxe);

        // update product Axe
        $product->setAxe($productAxe);

        // save product and user in db
        $em->merge($user);
        $em->merge($product);
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

        $cookie = $em->getRepository(Cookie::class)->findOneByName($cookieName);

        if(empty($cookie)) {
            throw new \Exception('Cookie not found !');
        }

        // Get user
        $user = $em->getRepository(User::class)->findOneByCookie($cookie);


        if(empty($user)) {
            throw new \Exception('User not found !');
        }

        $userAxe = $user->getAxe();

        if(is_null($userAxe)) {
            throw new \Exception('User Axe does not exist !');
        }

        // Get ticket
        $ticket = $em->getRepository(Ticket::class)->find($request->get('ticketId'));

        if(empty($ticket)) {
            throw new \Exception('Ticket not found !');
        }

        $ticketAxe = $ticket->getAxe();


        if(is_null($ticketAxe)) {
            // copy user Axe into ticket Axe
            $ticketAxe = new Axe();
            $ticketAxe->setAge($userAxe->getAge());
            $ticketAxe->setBrand($userAxe->getBrand());
            $ticketAxe->setCity($userAxe->getCity());
            $ticketAxe->setCsp($userAxe->getCsp());
            $ticketAxe->setFemale($userAxe->getFemale());
            $ticketAxe->setMale($userAxe->getMale());
            $ticketAxe->setSport($userAxe->getSport());
            $em->persist($ticketAxe);
            $ticket->setAxe($ticketAxe);
            $em->merge($ticket);
            $em->flush();
            return $user;
        }


        // calculate axes
        /*if($ticket->getIsFixed()) {
            if($userAxe->getMale() == 0 && $userAxe->getFemale() == 0 && $userAxe->getAge() == 0) { // if userAxe is not set
                $userAxe = $ticketAxe; // userAxe take $ticketAxe properties
            }
            else {
                $userAxe->setMale(($userAxe->getMale() + $ticketAxe->getMale()) / 2);
                $userAxe->setFemale(($userAxe->getFemale() + $ticketAxe->getFemale()) / 2);
                $userAxe->setAge(($userAxe->getAge() + $ticketAxe->getAge()) / 2);
                $userAxe->setCsp(($userAxe->getCsp() + $ticketAxe->getCsp()) / 2);

                //TODO: assign value to userAxe with means in clicks
                if($ticketAxe->getBrand() != null)
                    $userAxe->setBrand($ticketAxe->getBrand());
                if($ticketAxe->getCity() != null)
                    $userAxe->setCity($ticketAxe->getCity());
                if($ticketAxe->getSport() != null)
                    $userAxe->setSport($ticketAxe->getSport());
            }
        }
        else*/
        if($userAxe->getMale() != 0 && $userAxe->getFemale() != 0 && $userAxe->getAge() != 0) { // if userAxe is set and ticket is floating

            if($ticketAxe->getMale() == 0 && $ticketAxe->getFemale() == 0 && $ticketAxe->getAge() == 0) { // if ticket axe is not set
                // $ticketAxe take userAxe properties
                $ticketAxe->setMale($userAxe->getMale());
                $ticketAxe->setFemale($userAxe->getFemale());
                $ticketAxe->setAge($userAxe->getAge());
            }
            else {
                $ticketAxe->setMale(($ticketAxe->getMale() + $userAxe->getMale()) / 2);
                $ticketAxe->setFemale(($ticketAxe->getFemale() + $userAxe->getFemale()) / 2);
                $ticketAxe->setAge(($ticketAxe->getAge() + $userAxe->getAge()) / 2);
                $ticketAxe->setCsp(($ticketAxe->getCsp() + $userAxe->getCsp()) / 2);

                //TODO: assign value to ticketAxe with means in clicks
                if($userAxe->getBrand() != null)
                    $ticketAxe->setBrand($userAxe->getBrand());
                if($userAxe->getCity() != null)
                    $ticketAxe->setCity($userAxe->getCity());
                if($userAxe->getSport() != null)
                    $ticketAxe->setSport($userAxe->getSport());
            }
        }

        // update user Axe
        $user->setAxe($userAxe);

        // update product Axe
        $ticket->setAxe($ticketAxe);

        // save ticket and user in db
        $em->merge($user);
        $em->merge($ticket);
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