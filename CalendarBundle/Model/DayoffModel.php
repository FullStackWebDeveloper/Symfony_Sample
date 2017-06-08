<?php

namespace CalendarBundle\Model;

use CalendarBundle\Entity\Dayoff;
use CalendarBundle\Form\Object\DayOffRangeObject;
use CalendarBundle\Repository\DayoffRepository;

use DateInterval;
use DateTime;

use Doctrine\ORM\EntityManager;
use NoticeBundle\Model\NoticeModel;
use UserBundle\Entity\User;

class DayoffModel
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var NoticeModel
     */
    protected $noticeModel;

    /**
     * @var DayoffRepository
     */
    protected $repository;

    /**
     * @param EntityManager $em
     * @param NoticeModel   $noticeModel
     */
    public function __construct($em, $noticeModel)
    {
        $this->em          = $em;
        $this->noticeModel = $noticeModel;
        $this->repository  = $this->em->getRepository('CalendarBundle:Dayoff');
    }

    /**
     * @return DayoffRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository('CalendarBundle:Dayoff');
    }

    /**
     * @param User $user
     * @return DayOffRangeObject
     */
    public function createDayOffRangeObject($user)
    {
        $dayOffRange = new DayOffRangeObject();
        $dayOffRange->user = $user;

        return $dayOffRange;
    }

    /**
     * @param DayOffRangeObject $dayOffRange
     */
    public function createDayOffs($dayOffRange)
    {
        $day  = DateInterval::createFromDateString('1 days');
        $code = uniqid();

        for ($date = clone($dayOffRange->getStartDate()); $date <= $dayOffRange->getEndDate(); $date->add($day)) {
            $dayOff = new Dayoff();
            $dayOff->setDate(clone $date);
            $dayOff->setComment($dayOffRange->comment);
            $dayOff->setType($dayOffRange->type);
            $dayOff->setUser($dayOffRange->user);
            $dayOff->setCode($code);

            $this->em->persist($dayOff);

            $this->noticeModel->createDayoffNotice($dayOff);
        }

        $this->em->flush();
    }

    /**
     * @param string $code
     * @param User   $user
     *
     * @return DayOffRangeObject
     */
    public function getDayOffRange($code, $user)
    {
        $dayOff = $this->getDayOff($code, $user);

        $lastDayOff = $this->repository->getDayOffByCode($code, false);

        $dayOffRange = new DayOffRangeObject();
        $dayOffRange->startDate = $dayOff->getDate();
        $dayOffRange->endDate   = $lastDayOff->getDate();
        $dayOffRange->comment   = $dayOff->getComment();
        $dayOffRange->type      = $dayOff->getType();
        $dayOffRange->user      = $dayOff->getUser();
        $dayOffRange->code      = $dayOff->getCode();

        return $dayOffRange;
    }

    /**
     * @param string $code
     * @param User   $user
     *
     * @return Dayoff
     */
    public function getDayOff($code, $user)
    {
        $dayOff = $this->repository->getDayOffByCode($code);

        if (!$dayOff) {
            return null;
        }

        if (!$user->hasRole('ROLE_ADMIN') && $dayOff->getUser()->getId() === $user->getId()) {
            return null;
        }

        return $dayOff;
    }

    /**
     * @param DayOffRangeObject $dayOffRange
     * @param DateTime          $startDate
     * @param DateTime          $endDate
     */
    public function updateDayOffs($dayOffRange, $startDate, $endDate)
    {
        $this->em->beginTransaction();

        $this->doDeleteDayOffs($dayOffRange->code, $startDate, $endDate, $dayOffRange->user);
        $this->createDayOffs($dayOffRange);

        $this->em->commit();
    }

    /**
     * @param DayOff $dayOff
     */
    public function deleteDayOffs($dayOff)
    {
        $lastDayOff = $this->repository->getDayOffByCode($dayOff->getCode(), false);

        $this->em->beginTransaction();

        $this->doDeleteDayOffs($dayOff->getCode(), $dayOff->getDate(), $lastDayOff->getDate(), $dayOff->getUser());

        $this->em->commit();
    }

    /**
     * @param string   $code
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param User     $user
     */
    protected function doDeleteDayOffs($code, $startDate, $endDate, $user)
    {
        $this->repository->deleteByCode($code);
        $this->noticeModel->removeNotices($startDate, $endDate, $user);
    }

    /**
     * @param DateTime $startDate
     * @param User     $user
     * @param DateTime $endDate
     *
     * @return Dayoff[]
     */
    public function getDayOffsByUserAndDates($startDate, $user, $endDate = null)
    {
        return $this->repository->getDayOffsByUserAndDates($startDate, $user, $endDate);
    }

    /**
     * @param DateTime $startDate
     * @param User     $user
     * @param DateTime $endDate
     *
     * @return array
     */
    public function getDayOffsDates($startDate, $endDate, $user)
    {
        $dayOffs = $this->getDayOffsByUserAndDates($startDate, $user, $endDate);
        $result  = [];

        foreach ($dayOffs as $dayOff) {
            $result[$dayOff->getDate()->format('d')] = $dayOff->getType();
        }

        return $result;
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param User     $user
     *
     * @return array
     */
    public function getDayOffsRange($startDate, $endDate, $user)
    {
        return $this->repository->getDayOffRange($startDate, $endDate, $user);
    }
}