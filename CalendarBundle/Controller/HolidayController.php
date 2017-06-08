<?php

namespace CalendarBundle\Controller;


use CalendarBundle\Model\CalendarModel;
use CalendarBundle\Model\HolidayModel;
use CalendarBundle\Service\CalendarService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HolidayController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function indexAction()
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }

        $now = new DateTime('now');

        return $this->redirectToRoute('holidays', [
            'year'  => $now->format('Y'),
            'month' => $now->format('m')
        ]);
    }

    /**
     * @param int $year
     * @param int $month
     *
     * @return RedirectResponse|Response
     */
    public function holidaysAction($year, $month, Request $request)
    {
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }

        $startDate = new DateTime('01.' . $month . '.' . $year);

        /** @var CalendarModel $model */
        $model    = $this->get('calendar.model.calendar');
        $calendar = $model->getCalendar($startDate);

        return $this->render('CalendarBundle::holidays.html.twig', [
            'calendar' => $calendar,
            'service'  => $model->getCalendarService()
        ]);
    }

    /**
     * @param int     $year
     * @param int     $month
     * @param Request $request
     *
     * @return HolidayController|JsonResponse
     */
    public function holidaysUpdateAction($year, $month, Request $request){

        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }

        /** @var HolidayModel $model */
        $model = $this->get('calendar.model.holiday');
        $days  = explode(',', $request->get('days'));

        $model->saveHolidays($days, $month, $year);

        if(!($request->get('return') == 'false')) {
            return $this->redirectToRoute('holidays', [
                'month' => $month, 'year' => $year
            ]);
        } else {
            return new JsonResponse([
                'success' => 'true'
            ]);
        }
    }

}