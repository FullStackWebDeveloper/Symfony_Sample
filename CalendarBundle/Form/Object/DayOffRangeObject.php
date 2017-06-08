<?php

namespace CalendarBundle\Form\Object;

use DateTime;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use UserBundle\Entity\User;

class DayOffRangeObject
{
    /**
     * @var string
     */
    public $comment;

    /**
     * @var string
     */
    public $type;

    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var DateTime
     */
    public $endDate;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $code;

    /**
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
        if (is_string($startDate)) {
            $startDate = new DateTime($startDate);
        }

        $this->startDate = $startDate;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
        if (is_string($endDate)) {
            $endDate = new DateTime($endDate);
            $endDate->setTime(23, 59, 59);
        }

        $this->endDate = $endDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}