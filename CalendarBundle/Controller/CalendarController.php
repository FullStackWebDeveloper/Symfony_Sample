<?php

namespace CalendarBundle\Controller;

use CalendarBundle\Model\DayoffModel;
use DateTime;

use CalendarBundle\Entity\Dayoff;
use CalendarBundle\Model\CalendarModel;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalendarController extends Controller
{
    /**
     * @return CalendarModel
     */
    protected function getModel()
    {
        return $this->get('calendar.model.calendar');
    }

    /**
     * @param int     $year
     * @param int     $month
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function calendarAction($year, $month, Request $request)
    {
        $currentUser = $this->getUser();

        if (!($currentUser->hasRole('ROLE_DEVELOPER') || $currentUser->hasRole('ROLE_ADMIN'))) {
            return $this->redirectToRoute('homepage');
        }

        $calendarModel = $this->getModel();

        $user = $calendarModel->getUser($currentUser, $request->get('id'));

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $developers = $this->get('fos_user.user_manager')->getDevelopers();

        $startDate = new DateTime('01.'.$month.'.'.$year);
        $endDate   = new DateTime($startDate->format('t.m.Y'));

        /** @var DayoffModel $dayOffModel */
        $dayOffModel = $this->get('calendar.model.dayoff');

        $dayOffs      = $dayOffModel->getDayOffsDates($startDate, $endDate, $user);
        $dayOffsRange = $dayOffModel->getDayOffsRange($startDate, $endDate, $user);

        $calendar = $calendarModel->getUserCalendar($user, $startDate, $endDate);

        return $this->render('CalendarBundle::calendar.html.twig', [
            'calendar'     => $calendar,
            'service'      => $calendarModel->getCalendarService(),
            'user'         => $user,
            'users'        => $developers,
            'daysoff'      => $dayOffs,
            'daysoffRange' => $dayOffsRange,
            'types'        => Dayoff::$dayoffType
        ]);
    }

}
