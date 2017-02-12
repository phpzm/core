<?php

namespace Simples\Core\Helper;

use DateTime;
use DateInterval;

/**
 * Class Date
 * @package Simples\Core\Helper
 */
class Date extends DateTime
{
    /**
     * @var string
     */
    private static $format = 'Y-m-d';

    /**
     * Date constructor.
     * @param string $time
     * @param string $format (null)
     */
    public function __construct(string $time = 'today', string $format = null)
    {
        parent::__construct($time);

        static::$format = of($format, static::$format);
    }

    /**
     * @param string $time
     * @param string $format (null)
     * @return Date
     */
    public static function create(string $time = 'today', string $format = null): Date
    {
        return new static($time, $format);
    }

    /**
     * @return string
     */
    public function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @param $date
     * @return bool
     */
    public static function isDate($date)
    {
        $temp = self::createFromFormat(static::$format, $date);
        return $temp && $temp->format(static::$format) === $date;
    }

    /**
     * @param array $holidays Array with dates formatted with `d/m`
     * @param int $forward Minimum of days to add
     * @param array $weekend Days of week what will be like holidays
     * @return string
     */
    public function next(array $holidays = [], int $forward = 0, $weekend = ['0', '6'])
    {
        $search = true;
        $dated = 0;

        do {
            $week = $this->format('w');
            $day = $this->format('d/m');

            $is_weekend = in_array($week, $weekend);
            $is_holiday = in_array($day, $holidays);
            $is_dated = $dated >= $forward;

            if (!$is_weekend && !$is_holiday && $is_dated) {
                return $this->toString();
            }

            $this->addDays(1);
            if (!$is_weekend && !$is_holiday) {
                $dated++;
            }
        } while ($search);

        return $this->toString();
    }

    /**
     * @param $date
     * @return int
     */
    public function time($date)
    {
        return strtotime($date);
    }

    /**
     * @param $days
     * @return string
     */
    public function addDays($days)
    {
        $this->add(new DateInterval("P{$days}D"));

        return $this->toString();
    }

    /**
     * @param string $compare
     * @param bool $absolute
     * @return int
     */
    public function diffDays($compare = 'today', $absolute = false)
    {
        if (!($compare instanceOf DateTime)) {
            $compare = new DateTime($compare);
        }
        return (int)parent::diff($compare, $absolute)->format('%d');
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->format(static::$format);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
