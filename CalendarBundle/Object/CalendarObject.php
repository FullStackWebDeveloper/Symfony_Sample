<?php

namespace CalendarBundle\Object;

class CalendarObject
{
    /**
     * @var CalendarDayObject
     */
    public $days;

    /**
     * @var string
     */
    public $totalHours;

    /**
     * @var int
     */
    public $workDays;

    /**
     * @var int
     */
    public $workDaysBefore;
}