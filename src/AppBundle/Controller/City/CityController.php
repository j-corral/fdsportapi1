<?php

namespace AppBundle\Controller\City;

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

/**
 * CityController
 */
class CityController extends ControllerBase {

    /**
     * @ApiDoc(
     *      resource=true, section="City",
     *      description="Get nearby cities.",
     *      output= { "class"="", "collection"=false, "groups"={"base"} }
     * )
     *
     * @Rest\View(serializerGroups={"base"})
     * @Rest\Get("/city/{city}")
     * @QueryParam(name="distance", requirements="\d+", default="50000", description="Max distance to search for, in meters")
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @return array|mixed
     */
    public function getNearbyCitiesAction(Request $request, ParamFetcher $paramFetcher) {

        $city = $request->get('city');

        $distance = $paramFetcher->get('distance');

        $destinations = array(
            "Lille, France",
            "Strasbourg, France",
            "Paris, France",
            "Nantes, France",
            "Lyon, France",
            "Bordeaux, France",
            "Marseille, France",
            "Aubagne, France",
        );

        $maps = new MAPSAPI($this->getParameter('googleApiKey'));

        return $maps->getCitiesByMaxDistance($city, $destinations, $distance);
    }

    /**
     * @ApiDoc(
     *      resource=true, section="Absence",
     *      description="Get the absences of the subordinates of a user.",
     *      output= { "class"=Absence::class, "collection"=false, "groups"={"base", "absence"} }
     * )
     *
     * @Rest\View(serializerGroups={"base", "absence"})
     * @Rest\Get("/users/{userId}/subordinateabsences")
     */
     /*
    public function getSubordinateAbsenceAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($request->get(self::USER_ID));

        if (empty($user)) {
            throw $this->getUserNotFoundException();
        }


        $subordinateContract = $em->getRepository(UserContract::class)->findBy(
                array('manager' => $request->get(self::USER_ID))
        );

        $ourContract = $em->getRepository(UserContract::class)->findOneBy(
                array('user' => $request->get(self::USER_ID))
        );

        $listOfAbsences = array();

        if ($ourContract != null) {
            if ($ourContract->getManager() == null) {
                $absences = $em->getRepository(Absence::class)->findByUser($user);
                foreach ($absences as $absence) {
                    array_push($listOfAbsences, $absence);
                }
            }
        }

        foreach ($subordinateContract as $contract) {
            $absences = $em->getRepository(Absence::class)->findByUser($contract->getUser());
            foreach ($absences as $absence) {
                array_push($listOfAbsences, $absence);
            }
        }

        // return $em->getRepository(Absence::class)->findByUser($user);
        return $listOfAbsences;
    }
    */

    /**
     * @ApiDoc(
     *      resource=true, section="Absence",
     *      description="Add a new request of absence for a period. Users can only make a request for themself.",
     *      input={"class"=AbsenceType::class, "name"=""},
     *      statusCodes = {
     *          201 = "Created",
     *          400 = "Bad Request"
     *      },
     *      responseMap={
     *          201 = { "class"=Absence::class, "groups"={"base", "absence"}},
     *          400 = { "class"=AbsenceType::class, "fos_rest_form_errors"=true, "name" = ""}
     *      }
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"base", "absence"})
     * @Rest\Post("/users/{userId}/absences")
     */
     /*
    public function postAbsenceAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($request->get(self::USER_ID));

        if (empty($user)) {
            throw $this->getUserNotFoundException();
        }

        if ($user->getId() != $this->getUser()->getId()) {
            throw $this->getUserNotFoundException();
        }

        $absence = new Absence();
        $form = $this->createForm(AbsenceType::class, $absence);

        $form->submit($request->request->all());

        if ($form->isValid()) {

            // TODO : Refactor this part. We must not refer to the title of the absence type.
            // We should add an attribute "isHourAllowed" to the absence type.
            if ($absence->getAbsenceType()->getTitle() == 'CP' && $absence->getStartHour() != null && $absence->getEndHour() != null) {
                throw $this->getRttHourBadRequestHttpException();
            }

            $absence->setUser($user);
            $absence->setState(AbsenceState::REQUESTED);
            $absence->setCountDays($this->countDaysOfAbsence($user, $absence));
            $em->persist($absence);
            $em->flush();

            return $absence;
        } else {
            return $form;
        }
    }
*/

    /**
     * @ApiDoc(
     *      resource=true, section="Absence",
     *      description="Partialy update the informations of a user absence. Only the manager of the user can APPROVE or REFUSE an absence.",
     *      input={"class"=AbsencePatchType::class, "name"=""},
     *      statusCodes = {
     *          200 = "OK",
     *          404 = "Not Found",
     *          400 = "Bad Request"
     *      },
     *      responseMap={
     *          200 = { "class"=Absence::class, "groups"={"base", "absence"}},
     *          400 = { "class"=AbsencePatchType::class, "fos_rest_form_errors"=true, "name" = ""}
     *      }
     * )

     * @Rest\View(serializerGroups={"base", "absence"})
     * @Rest\Patch("/users/{userId}/absences/{id}")
     */
     /*
    public function patchAbsenceAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($request->get('userId'));

        if (empty($user)) {
            throw $this->getUserNotFoundException();
        }

        $absence = $em->getRepository(Absence::class)->find($request->get('id'));

        // if (empty($absence) || $absence->getUser()->getId() != $user->getId()) {
        if (empty($absence)) {
            throw new NotFoundHttpException($this->trans('absence.error.notFound'));
        }

        if (!$this->isAllowedToPatchUsersAbsence($user)) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(AbsencePatchType::class, $absence, []);

        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $em->persist($absence);
            $em->flush();

            return $absence;
        } else {
            return $form;
        }
    }
*/

    /**
     * @ApiDoc(
     *      resource=true, section="Absence",
     *      description="Delete an absence. Users can only delete there absences. Absence can be deleted only when Pending",
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/users/{userId}/absences/{id}")
     */
     /*
    public function removeAbsenceAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($request->get(self::USER_ID));

        if (empty($user)) {
            throw $this->getUserNotFoundException();
        }

        $absence = $em->getRepository(Absence::class)
                ->find($request->get('id'));

        if (!$absence) {
            throw $this->getAbsenceNotFoundException();
        }

        if($absence->getUser()->getId() != $user->getId()){
            throw $this->getAbsenceNotFoundException();
        }
        if($absence->getUser()->getId() != $this->getUser()->getId() || $absence->getState() != AbsenceState::REQUESTED){
            throw new AccessDeniedHttpException();
        }

        $em->remove($absence);
        $em->flush();
    }
*/


/*
    private function getUserNotFoundException() {
        return new NotFoundHttpException($this->trans('user.error.notFound'));
    }

    private function getAbsenceNotFoundException() {
        return new NotFoundHttpException($this->trans('absence.error.notFound'));
    }

    private function getRttHourBadRequestHttpException() {
        return new BadRequestHttpException($this->trans('absence.error.onlyrttwithhour'));
    }
*/

}
