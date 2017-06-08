<?php

namespace CalendarBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class DayOffRange extends Constraint
{
    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}