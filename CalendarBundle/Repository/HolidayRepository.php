<?php

namespace CalendarBundle\Repository;

use CalendarBundle\Entity\Holiday;
use Doctrine\ORM\EntityRepository;

class HolidayRepository extends EntityRepository
{
    /**
     * @param $startDate
     * @param $endDate
     *
     * @return Holiday[]
     */
    public function getHolidaysByDates($startDate, $endDate)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.date >= :startDate')
            ->andWhere('h.date <= :endDate')
            ->setParameters([
                'startDate' => $startDate,
                'endDate'   => $endDate
            ])
            ->getQuery()
            ->getResult();
    }

}