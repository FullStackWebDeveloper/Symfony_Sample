<?php

namespace CalendarBundle\Form\DataTransformer;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;

class DateToStringTransformer implements DataTransformerInterface
{
    /**
     * @param DateTime $date
     * @return string
     */
    public function transform($date)
    {
        if ($date = null) {
            return '';
        }

        return $date->format('d.m.Y');
    }

    /**
     * @param string $dateString
     * @return DateTime
     */
    public function reverseTransform($dateString)
    {
        $date = new DateTime($dateString);

        return $date;
    }
}