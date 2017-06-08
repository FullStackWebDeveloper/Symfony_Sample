<?php

namespace CalendarBundle\Helper;

use DateTime;

class DateTimeHelper {

    /**
     * @param string $startDate
     * @param string $endDate
     *
     * @return DateTime[]
     */
    public static function getStartAndEndDates($startDate, $endDate){

        $dates['startDate'] = new \DateTime($startDate);

        $dates['endDate'] = new \DateTime($endDate);
        $dates['endDate']->setTime(23,59,59);

        return $dates;
    }

    /**
     * @param Datetime $date
     * @return DateTime
     */
    public static function getWeekStart($date = null)
    {
        if (is_null($date)) {
            $date = new DateTime();
        } else {
            $date = clone($date);
        }

        $date->setISODate($date->format('Y'), $date->format('W'), 1);

        return $date;
    }

    /**
     * @param Datetime $date
     * @return DateTime
     */
    public static function getWeekEnd($date = null)
    {
        if (is_null($date)) {
            $date = new DateTime();
        } else {
            $date = clone($date);
        }

        $date->setISODate($date->format('Y'), $date->format('W'), 7);

        return $date;
    }

    /**
     * @param Datetime $date
     * @return bool
     */
    public static function isDayOff($date)
    {
        return $date->format('N') > 5;
    }
}