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
    public static function today(): string
    {
        return date('Y-m-d');
    }

    /**
     * @return string
     */
    public static function now()
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
     * @param string $date
     * @param int $months
     * @return string
     */
    public static function nextMonth(string $date, int $months): string
    {
        return date('Y-m-d', strtotime($date . " +" . $months . " month"));
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
        if (!($compare instanceof DateTime)) {
            $compare = new DateTime($compare);
        }
        return (int)parent::diff($compare, $absolute)->format('%d');
    }

    /**
     * @return string
     */
    protected function toString()
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
