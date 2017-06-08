<?php

namespace CalendarBundle\Repository;

use CalendarBundle\Entity\Dayoff;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use UserBundle\Entity\User;

class DayoffRepository extends EntityRepository
{

    /**
     * @param DateTime $startDate
     * @param User     $user
     * @param DateTime $endDate
     * @param string   $code
     *
     * @return QueryBuilder
     */
    public function getDayOffsByUserAndDatesQuery($startDate, $user, $endDate = null, $code = null)
    {
        $query = $this->createQueryBuilder('d')
            ->where('d.date >= :startDate')
            ->andWhere('d.user = :user')
            ->setParameters([
                'startDate' => $startDate,
                'user'      => $user
            ]);

        if (!is_null($endDate)) {
            $query->andWhere('d.date < :endDate')
                ->setParameter('endDate', $endDate);
        }

        if ($code) {
            $query->andWhere('d.code <> :code')
                ->setParameter('code', $code);
        }

        return $query;
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
        return $this->getDayOffsByUserAndDatesQuery($startDate, $user, $endDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User     $user
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string   $code
     *
     * @return bool
     */
    public function hasUserDayOff($user, $startDate, $endDate = null, $code = null)
    {
        $dayOff = $this->getDayOffsByUserAndDatesQuery($startDate, $user, $endDate, $code)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $dayOff ? true : false;
    }

    /**
     * @param string $code
     * @param bool   $first
     *
     * @return DayOff
     */
    public function getDayOffByCode($code, $first = true)
    {
        $order = $first ? 'ASC' : 'DESC';

        return $this->createQueryBuilder('d')
            ->where('d.code = :code')
            ->setParameter('code', $code)
            ->setMaxResults(1)
            ->orderBy('d.date', $order)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $code
     */
    public function deleteByCode($code)
    {
        $this->_em->createQueryBuilder()
            ->delete($this->_entityName, 'd')
            ->where('d.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->execute();
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param User     $user
     *
     * @return array
     */
    public function getDayOffRange($startDate, $endDate, $user){

        return $this->createQueryBuilder('d')
            ->select('d.type, min(d.date) as minDate, max(d.date) as maxDate, d.comment')
            ->where('d.date >= :startDate')
            ->andWhere('d.date <= :endDate')
            ->andWhere('d.user = :user')
            ->setParameters([
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'user'      => $user
            ])
            ->groupBy('d.code')
            ->orderBy('d.type, minDate')
            ->getQuery()
            ->getResult();
    }


}