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
use AppBundle\Entity\Brand;
use AppBundle\Entity\Sport;
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

        return $this->getClickByUser($user);
    }


    /**
     * @param $user
     * @param string $column
     * @return array
     */
    private function getClickByUser($user, $column = '') {
        $em = $this->getDoctrine()->getManager();

        if(empty($column)) {
            $click = $em->getRepository(Click::class)->findBy([
                "user" => $user
            ], ["click_id" => "DESC"]);
        } else {
            $click = $em->getRepository(Click::class)->findByColumn($user, $column);
        }

        if (empty($click)) {
            throw $this->getClickNotFoundException();
        }

        return $click;
    }


    /**
     * @param $product
     * @return array
     */
    private function getClickByProduct($product) {
        $em = $this->getDoctrine()->getManager();

        $click = $em->getRepository(Click::class)->findBy([
            "product" => $product
        ], ["click_id" => "DESC"]);

        /*if (empty($click)) {
            throw $this->getClickNotFoundException();
        }*/

        return $click;
    }


    /**
     * @param $ticket
     * @return array
     */
    private function getClickByTicket($ticket) {
        $em = $this->getDoctrine()->getManager();

        $click = $em->getRepository(Click::class)->findBy([
            "ticket" => $ticket
        ], ["click_id" => "DESC"]);

        /*if (empty($click)) {
            throw $this->getClickNotFoundException();
        }*/

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
                // userAxe take productAxe properties
                $userAxe->setMale($productAxe->getMale());
                $userAxe->setFemale($productAxe->getFemale());
                $userAxe->setAge($productAxe->getAge());
                $userAxe->setCsp($productAxe->getCsp());
                $userAxe->setBrand($productAxe->getBrand());
                $userAxe->setCity($productAxe->getCity());
                $userAxe->setSport($productAxe->getSport());
            }
            else {
                // Get array with user clicks
                $clicks = $this->getClickByUser($user, 'product');


                $maleSum = 0;
                $femaleSum = 0;
                $ageSum = 0;
                $cspSum = 0;
                foreach ($clicks as $click) {
                    if(!empty($click->getProduct()) && !empty($click->getProduct()->getAxe())) {
                        $maleSum += $click->getProduct()->getAxe()->getMale();
                        $femaleSum += $click->getProduct()->getAxe()->getFemale();
                        $ageSum += $click->getProduct()->getAxe()->getAge();
                        $cspSum += $click->getProduct()->getAxe()->getCsp();
                    }
                }

                $nbClicks = count($clicks);

                if($nbClicks > 0) {
                    $userAxe->setMale($maleSum / $nbClicks);
                    $userAxe->setFemale($femaleSum / $nbClicks);
                    $userAxe->setAge($ageSum / $nbClicks);
                    $userAxe->setCsp($cspSum / $nbClicks);
                }

                // On compte le nombre d'occurences de chacune des chaines de caractères
                $countsBrand = array();
                $countsCity = array();
                $countsSport = array();

                foreach ($clicks as $click) {
                    if(!empty($click->getProduct()) && !empty($click->getProduct()->getAxe())) {
                        if($click->getProduct()->getAxe()->getBrand() && !empty($click->getProduct()->getAxe()->getBrand()->getName())) {
                            $countsBrand[] = $click->getProduct()->getAxe()->getBrand()->getName();
                        }

                        if(!empty($click->getProduct()->getAxe()->getCity())) {
                            $countsCity[] = $click->getProduct()->getAxe()->getCity();
                        }

                        if($click->getProduct()->getAxe()->getSport() && !empty($click->getProduct()->getAxe()->getSport()->getName())) {
                            $countsSport[] = $click->getProduct()->getAxe()->getSport()->getName();
                        }
                    }
                }


                // compte le nombre d'entrée par clef
                $countsBrand = array_count_values($countsBrand);
                $countsCity = array_count_values($countsCity);
                $countsSport = array_count_values($countsSport);

                $brand = $city = $sport = '';

                if(!empty($countsBrand)) {
                    // On récupère la première clé qui correspond au plus grand count
                    $brand = array_keys($countsBrand, max($countsBrand))[0];
                    // On assigne les valeurs
                    $userAxe->setBrand($em->getRepository(Brand::class)->findOneByName($brand));
                }

                if(!empty($countsCity)) {
                    $city = array_keys($countsCity, max($countsCity))[0];
                    $userAxe->setCity($city);
                }

                if(!empty($countsSport)) {
                    $sport = array_keys($countsSport, max($countsSport))[0];
                    $userAxe->setSport($em->getRepository(Sport::class)->findOneByName($sport));
                }

            }
        }
        else if($userAxe->getMale() != 0 && $userAxe->getFemale() != 0 && $userAxe->getAge() != 0) { // if userAxe is set and product is floating

            if($productAxe->getMale() == 0 && $productAxe->getFemale() == 0 && $productAxe->getAge() == 0) { // if product axe is not set
                // productAxe take userAxe properties
                $productAxe->setAge($userAxe->getAge());
                $productAxe->setBrand($userAxe->getBrand());
                $productAxe->setCity($userAxe->getCity());
                $productAxe->setCsp($userAxe->getCsp());
                $productAxe->setFemale($userAxe->getFemale());
                $productAxe->setMale($userAxe->getMale());
                $productAxe->setSport($userAxe->getSport());
            }
            else {

                // Get array with product clicks
                $clicks = $this->getClickByProduct($product);

                $maleSum = 0;
                $femaleSum = 0;
                $ageSum = 0;
                $cspSum = 0;

                foreach ($clicks as $click) {
                    $maleSum += $click->getUser()->getAxe()->getMale();
                    $femaleSum += $click->getUser()->getAxe()->getFemale();
                    $ageSum += $click->getUser()->getAxe()->getAge();
                    $cspSum += $click->getUser()->getAxe()->getCsp();
                }


                $nbClicks = count($clicks);

                if($nbClicks > 0) {
                    $productAxe->setMale($maleSum / $nbClicks);
                    $productAxe->setFemale($femaleSum / $nbClicks);
                    $productAxe->setAge($ageSum / $nbClicks);
                    $productAxe->setCsp($cspSum / $nbClicks);
                }

                // On compte le nombre d'occurences de chacune des chaines de caractères
                $countsBrand = array();
                $countsCity = array();
                $countsSport = array();
                foreach ($clicks as $click) {
                    if($click->getUser() && $click->getUser()->getAxe()) {

                        if($click->getUser()->getAxe()->getBrand() && !empty($click->getUser()->getAxe()->getBrand()->getName())) {
                            $countsBrand[] = $click->getUser()->getAxe()->getBrand()->getName();
                        }

                        if(!empty($click->getUser()->getAxe()->getCity())) {
                            $countsCity[] = $click->getUser()->getAxe()->getCity();
                        }

                        if($click->getUser()->getAxe()->getSport() && !empty($click->getUser()->getAxe()->getSport()->getName())) {
                            $countsSport[] = $click->getUser()->getAxe()->getSport()->getName();
                        }
                    }
                }

                // compte le nombre d'entrée par clef
                $countsBrand = array_count_values($countsBrand);
                $countsCity = array_count_values($countsCity);
                $countsSport = array_count_values($countsSport);


                $brand = $city = $sport = '';

                if(!empty($countsBrand)) {
                    // On récupère la première clé qui correspond au plus grand count
                    $brand = array_keys($countsBrand, max($countsBrand))[0];
                    // On assigne les valeurs
                    $productAxe->setBrand($em->getRepository(Brand::class)->findOneByName($brand));
                }

                if(!empty($countsCity)) {
                    $city = array_keys($countsCity, max($countsCity))[0];
                    $productAxe->setCity($city);
                }

                if(!empty($countsSport)) {
                    $sport = array_keys($countsSport, max($countsSport))[0];
                    $productAxe->setSport($em->getRepository(Sport::class)->findOneByName($sport));
                }

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
                $clicks = getClickByUser($user); // Get array with user clicks

                $maleSum = 0;
                $femaleSum = 0;
                $ageSum = 0;
                $cspSum = 0;
                foreach ($clicks as $click) {
                    $maleSum += $click->getTicket()->getAxe()->getMale();
                    $femaleSum += $click->getTicket()->getAxe()->getFemale();
                    $ageSum += $click->getTicket()->getAxe()->getAge();
                    $cspSum += $click->getTicket()->getAxe()->getCsp();
                }

                $userAxe->setMale($maleSum / $clicks.size());
                $userAxe->setFemale($femaleSum / $clicks.size());
                $userAxe->setAge($ageSum / $clicks.size());
                $userAxe->setCsp($cspSum / $clicks.size());

                 // On compte le nombre d'occurences de chacune des chaines de caractères
                $countsBrand = array();
                $countsCity = array();
                $countsSport = array();
                foreach ($clicks as $click) {
                    $countsBrand[$click->getTicket()->getAxe()->getBrand()]++;
                    $countsCity[$click->getTicket()->getAxe()->getCity()]++;
                    $countsSport[$click->getTicket()->getAxe()->getSport()]++;
                }
                // On ordonne par ordre croissant
                sort($countsBrand, SORT_NUMERIC);
                sort($countsCity, SORT_NUMERIC);
                sort($countsSport, SORT_NUMERIC);

                // On récupère la première clé qui correspond au plus grand count
                $brand = array_keys($countsBrand, end($countsBrand))[0];
                $city = array_keys($countsCity, end($countsCity))[0];
                $sport = array_keys($countsCity, end($countsCity))[0];

                // On assigne les valeurs
                $userAxe->setBrand($brand);
                $userAxe->setCity($city);
                $userAxe->setSport($sport);
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
                // Get array with ticket clicks
                $clicks = $this->getClickByTicket($ticket);

                $maleSum = 0;
                $femaleSum = 0;
                $ageSum = 0;
                $cspSum = 0;
                foreach ($clicks as $click) {
                    $maleSum += $click->getUser()->getAxe()->getMale();
                    $femaleSum += $click->getUser()->getAxe()->getFemale();
                    $ageSum += $click->getUser()->getAxe()->getAge();
                    $cspSum += $click->getUser()->getAxe()->getCsp();
                }


                $nbClicks = count($clicks);

                if($nbClicks > 0) {
                    $ticketAxe->setMale($maleSum / $nbClicks);
                    $ticketAxe->setFemale($femaleSum / $nbClicks);
                    $ticketAxe->setAge($ageSum / $nbClicks);
                    $ticketAxe->setCsp($cspSum / $nbClicks);
                }


                // On compte le nombre d'occurences de chacune des chaines de caractères
                $countsBrand = array();
                $countsCity = array();
                $countsSport = array();

                foreach ($clicks as $click) {
                    if($click->getUser() && $click->getUser()->getAxe()) {

                        if($click->getUser()->getAxe()->getBrand() && !empty($click->getUser()->getAxe()->getBrand()->getName())) {
                            $countsBrand[] = $click->getUser()->getAxe()->getBrand()->getName();
                        }

                        if(!empty($click->getUser()->getAxe()->getCity())) {
                            $countsCity[] = $click->getUser()->getAxe()->getCity();
                        }

                        if($click->getUser()->getAxe()->getSport() && !empty($click->getUser()->getAxe()->getSport()->getName())) {
                            $countsSport[] = $click->getUser()->getAxe()->getSport()->getName();
                        }
                    }
                }


                $brand = $city = $sport = '';

                if(!empty($countsBrand)) {
                    // On récupère la première clé qui correspond au plus grand count
                    $brand = array_keys($countsBrand, max($countsBrand))[0];
                    // On assigne les valeurs
                    $ticketAxe->setBrand($em->getRepository(Brand::class)->findOneByName($brand));
                }

                if(!empty($countsCity)) {
                    $city = array_keys($countsCity, max($countsCity))[0];
                    $ticketAxe->setCity($city);
                }

                if(!empty($countsSport)) {
                    $sport = array_keys($countsSport, max($countsSport))[0];
                    $ticketAxe->setSport($em->getRepository(Sport::class)->findOneByName($sport));
                }

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