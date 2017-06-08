<?php

namespace CalendarBundle\Model;

use CalendarBundle\Helper\DateTimeHelper;
use DateTime;

use CalendarBundle\Repository\HolidayRepository;
use CalendarBundle\Entity\Holiday;

use NoticeBundle\Model\NoticeModel;

use Doctrine\ORM\EntityManager;

class HolidayModel
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var HolidayRepository
     */
    protected $repository;

    /**
     * @param EntityManager $em
     * @param NoticeModel   $noticeModel
     */
    public function __construct($em, $noticeModel)
    {
        $this->em          = $em;
        $this->repository  = $this->em->getRepository('CalendarBundle:Holiday');
        $this->noticeModel = $noticeModel;
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getHolidaysByDates($startDate, $endDate)
    {
        return $this->repository->getHolidaysByDates($startDate, $endDate);
    }

    /**
     * @param string[] $days
     * @param int      $month
     * @param int      $year
     */
    public function saveHolidays($days, $month, $year)
    {
        foreach ($days as $day) {
            $this->updateOrCreateHoliday(new DateTime($day.'.'.$month.'.'.$year));
        }
    }

    /**
     * @param DateTime $date
     */
    public function updateOrCreateHoliday($date)
    {
        $holiday = $this->repository->findOneBy(['date' => $date]);

        if (!$holiday) {
            $boolHoliday = DateTimeHelper::isDayOff($date);

            $holiday = new Holiday();
            $holiday->setDate($date);
            $holiday->setHoliday(!$boolHoliday);
        } else {
            $holiday->setHoliday(!$holiday->getHoliday());
        }

        $this->noticeModel->createHolidayNotice($date, $holiday->getHoliday());

        $this->em->persist($holiday);
        $this->em->flush();
    }
}