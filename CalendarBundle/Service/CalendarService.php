<?php

namespace CalendarBundle\Service;

use CalendarBundle\Helper\DateTimeHelper;
use DateInterval;
use DateTime;

use CalendarBundle\Entity\Holiday;
use CalendarBundle\Model\HolidayModel;
use CalendarBundle\Object\CalendarDayObject;
use CalendarBundle\Object\CalendarObject;

use ProjectBundle\Entity\Task;

class CalendarService
{
    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $endDate;

    /**
     * @var Holiday[]
     */
    private $holidays;

    /**
     * @var HolidayModel
     */
    private $holidayModel;

    /**
     * @param HolidayModel $holidayModel
     */
    public function __construct($holidayModel)
    {
        $this->holidayModel = $holidayModel;
    }

    /**
     * @param DateTime $date
     */
    protected function setup($date)
    {
        $this->date = $date;

        $this->startDate = new DateTime($this->date->format('01.m.Y'));
        $this->endDate   = new DateTime($this->date->format('t.m.Y'));

        $this->endDate->setTime(23, 59, 59);

        $this->holidays = $this->holidayModel->getHolidaysByDates($this->startDate, $this->endDate);
    }

    /**
     * @param DateTime $date
     * @param Task[]   $tasks
     * @return CalendarObject
     */
    public function getCalendar($date, $tasks = null)
    {
        $this->setup($date);

        $calendar  = new CalendarObject();

        $now = new DateTime('now');
        $day = DateInterval::createFromDateString('1 days');

        $workDays       = 0;
        $totalMinutes   = 0;
        $workDaysBefore = 0;

        for ($currentDate = clone($this->startDate); $currentDate <= $this->endDate; $currentDate->add($day)) {
            $dateDay = new CalendarDayObject($currentDate);

            if ($currentDate <= $now && !is_null($tasks)) {
                $minutes = 0;

                foreach ($tasks as $task) {
                    if ($task->getDate() == $currentDate) {
                        $minutes      += $task->getTime();
                        $totalMinutes += $minutes;
                    }
                }

                $dateDay->hour = $this->getTimeString($minutes);
            }

            if ($currentDate->format('W') >= $now->format('W')) {
                $dateDay->oldWeek = false;
            }

            if ($this->isHoliday($currentDate)) {
                $dateDay->holiday = true;
            } else {
                $workDays++;
            }

            if ($currentDate->format('d.m.Y') == $now->format('d.m.Y')) {
                $dateDay->today = true;
                $workDaysBefore = $workDays;
            }

            $calendar->days[] = $dateDay;
        }

        $calendar->totalHours = $this->getTimeString($totalMinutes);
        $calendar->workDays   = $workDays;

        if ($workDaysBefore == 0) {
            $calendar->workDaysBefore = $workDays;
        } else {
            $calendar->workDaysBefore = $workDaysBefore;
        }

        return $calendar;
    }

    /**
     * @param DateTime $date
     * @return bool
     */
    public function isHoliday($date)
    {
        foreach ($this->holidays as $holiday) {
            if ($holiday->getDate() == $date) {
                return $holiday->getHoliday();
            }
        }

        return DateTimeHelper::isDayOff($date);
    }

    /**
     * @return string
     */
    public function getEmptyDates()
    {
        $date = clone($this->startDate);

        return $date->format('N') - 1;
    }

    /**
     * @return string
     */
    public function getMonthName()
    {
        return $this->date->format('F');
    }

    /**
     * @param int $min
     * @return string
     */
    public function getTimeString($min)
    {
        if ($min % 60 < 10) {
            return intdiv($min, 60) . ':0' . $min % 60;
        } else {
            return intdiv($min, 60) . ':' . $min % 60;
        }
    }

    /**
     * @param null $startDate
     * @return DateTime|null
     */
    public function getWeekEnd($startDate = null)
    {
        if (is_null($startDate)) {
            $date = new DateTime();
        } else {
            $date = clone($startDate);
        }

        $date->setISODate($this->date->format('Y'), $this->date->format('W'), 7);

        return $date;
    }

    /**
     * @param $startDate
     * @return DateTime
     */
    public function getWeekStart ($startDate = null)
    {
        if (is_null($startDate)) {
            $date = new DateTime();
        } else {
            $date = clone($startDate);
        }

        $date->setISODate($this->date->format('Y'), $this->date->format('W'), 1);

        return $date;
    }

    /**
     * @return array
     */
    public function getNextMonth(){

        $date = clone($this->date);

        $nextMonth['year']  = $date->modify('next month')->format('Y');
        $nextMonth['month'] = $date->format('m');

        return $nextMonth;
    }

    /**
     * @return array
     */
    public function getPrevMonth()
    {
        $date = clone($this->date);

        $prevMonth['year']  = $date->modify('-1 month')->format('Y');
        $prevMonth['month'] = $date->format('m');

        return $prevMonth;
    }

    /**
     * @return array
     */
    public function getNextWeek()
    {
        $date = clone($this->date);

        $nextWeek['year'] = $date->modify('next week')->format('Y');
        $nextWeek['week'] = $date->format('W');

        return $nextWeek;
    }

    /**
     * @return array
     */
    public function getPrevWeek()
    {
        $date = clone($this->date);

        $prevWeek['year'] = $date->modify('-1 week')->format('Y');
        $prevWeek['week'] = $date->format('W');

        return $prevWeek;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}