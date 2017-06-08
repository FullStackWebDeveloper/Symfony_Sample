<?php

namespace CalendarBundle\Controller;

use CalendarBundle\Form\DayOffRangeType;
use CalendarBundle\Model\DayoffModel;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use DateTime;

class DayoffController extends Controller
{
    /**
     * @return DayoffModel
     */
    protected function getModel()
    {
        return $this->get('calendar.model.dayoff');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createDayOffAction(Request $request)
    {
        $model = $this->getModel();

        $form = $this->createForm(
            DayOffRangeType::class,
            $model->createDayOffRangeObject($this->getUser())
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model->createDayOffs($form->getData());

            return $this->redirectToRoute('homepage');
        }

        return $this->render('CalendarBundle::dayoff.html.twig' , [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getUserDayOffsAction($id = null)
    {
        if ($id) {
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($id);

            if (!$user) {
                return new JsonResponse(['success' => false]);
            }
        } else {
            $user = $this->getUser();
        }

        $dayOffs = $this->getModel()->getDayOffsByUserAndDates(new DateTime(), $user);

        $result = [
            'dates'   => [],
            'success' => true
        ];

        foreach ($dayOffs as $dayOff ) {
            $result['dates'][]  = $dayOff->getDate()->format('d.m.Y');
        }

        return new JsonResponse($result);
    }

    /**
     * @param string  $code
     * @param Request $request
     *
     * @return Response
     */
    public function editDayOffNewAction($code, Request $request)
    {
        $model = $this->getModel();

        $dayOffRange = $model->getDayOffRange($code, $this->getUser());

        if (!$dayOffRange) {
            throw new NotFoundHttpException();
        }

        $startDate = $dayOffRange->startDate;
        $endDate   = $dayOffRange->endDate;

        $form = $this->createForm(DayOffRangeType::class, $dayOffRange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model->updateDayOffs($form->getData(), $startDate, $endDate);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('CalendarBundle::dayoff_edit.html.twig', [
            'form'  => $form->createView()
        ]);
    }

    /**
     * @param string $code
     * @return RedirectResponse
     */
    public function deleteDayOffsAction($code)
    {
        $model = $this->getModel();

        $dayOff = $model->getDayOff($code, $this->getUser());

        if (!$dayOff) {
            throw new NotFoundHttpException();
        }

        $model->deleteDayOffs($dayOff);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editDayoffAction($id, Request $request){
    }

    /**
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteDayoffRangeAction($id, Request $request){
    }

}