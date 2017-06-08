<?php

namespace CalendarBundle\Validator\Constraints;

use CalendarBundle\Repository\DayoffRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContext;
use Doctrine\ORM\EntityManager;

/**
 * @property ExecutionContext $context
 */
class DayOffRangeValidator extends ConstraintValidator
{
    /**
     * @var DayoffRepository
     */
    protected $repository;

    /**
     * @param EntityManager $em
     */
    public function __construct($em)
    {
        $this->repository = $em->getRepository('CalendarBundle:Dayoff');
    }

    /**
     * @param DayOffRange $dayOffRange
     * @param Constraint  $constraint
     */
    public function validate($dayOffRange, Constraint $constraint)
    {
        if ($dayOffRange->startDate > $dayOffRange->endDate) {
            $this->context->buildViolation('Start date must be greater than end date')
                ->atPath('startDate')
                ->addViolation();

            return;
        }

        $hasUserDayoff = $this->repository->hasUserDayOff(
            $dayOffRange->user,
            $dayOffRange->startDate,
            $dayOffRange->endDate,
            $dayOffRange->code
        );

        if ($hasUserDayoff) {
            $this->context->buildViolation('Plese select another dates')
                ->atPath('startDate')
                ->addViolation();
        }
    }
}