<?php

namespace CalendarBundle\Object;

use DateTime;

class CalendarDayObject {

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $hour;

    /**
     * @var bool
     */
    public $holiday = false;

    /**
     * @var bool
     */
    public $oldWeek = true;

    /**
     * @var bool
     */
    public $today = false;

    /**
     * @param DateTime $date
     */
    public function __construct($date)
    {
        $this->date = $date->format('d');
    }

}