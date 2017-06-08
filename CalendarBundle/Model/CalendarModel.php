<?php

namespace CalendarBundle\Model;

use DateTime;

use CalendarBundle\Object\CalendarObject;
use CalendarBundle\Service\CalendarService;

use ProjectBundle\Model\TaskModel;

use UserBundle\Entity\User;
use UserBundle\Model\UserModel;

class CalendarModel
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var CalendarService
     */
    private $calendarService;

    /**
     * @var TaskModel
     */
    private $taskModel;

    /**
     * @param UserModel       $userModel
     * @param CalendarService $calendarService
     * @param TaskModel       $taskModel
     */
    public function __construct($userModel, $calendarService, $taskModel)
    {
        $this->userModel       = $userModel;
        $this->calendarService = $calendarService;
        $this->taskModel       = $taskModel;
    }

    /**
     * @param User $currentUser
     * @param int  $id
     *
     * @return User|null
     */
    public function getUser($currentUser, $id = null)
    {
        if ($currentUser->hasRole('ROLE_ADMIN')) {
            if ($id) {
                return $this->userModel->getDeveloper($id);
            }

            if ($currentUser->hasRole('ROLE_DEVELOPER')) {
                return $currentUser;
            } else {
                return $this->userModel->getFirstDeveloper();
            }
        } else {
            return $currentUser;
        }
    }

    /**
     * @param User     $user
     * @param DateTime $startDate
     * @param DateTime $endDate
     *
     * @return CalendarObject
     */
    public function getUserCalendar($user, $startDate, $endDate)
    {
        $tasks = $this->taskModel->getTasks($user, $startDate, $endDate);

        return $this->calendarService->getCalendar($startDate, $tasks);
    }

    /**
     * @param DateTime $startDate
     *
     * @return CalendarObject
     */
    public function getCalendar($startDate)
    {
        return $this->calendarService->getCalendar($startDate);
    }

    /**
     * @return CalendarService
     */
    public function getCalendarService()
    {
        return $this->calendarService;
    }
}