<?php

namespace Simples\Core\Helper;

/**
 * Class Date
 * @package Simples\Core\Helper
 */
class Date extends \DateTime
{
    /**
     * @var string
     */
    private $format = 'Y-m-d';

    /**
     * Date constructor.
     * @param string $format
     */
    public function __construct($format = null)
    {
        parent::__construct();

        $this->format = of($format, $this->format);
    }

    /**
     * @param string $format
     * @return static
     */
    public static function create($format = null)
    {
        return new static($format);
    }

    /**
     * @param string $format
     * @return string
     */
    public function current($format = null)
    {
        return date(of($format, $this->format));
    }

    /**
     * @param $date
     * @return bool
     */
    public function isDate($date)
    {
        $d = self::createFromFormat($this->format, $date);
        return $d && $d->format($this->format) === $date;
    }

    /**
     * @param $date
     * @param $holidays
     * @param $days
     * @return mixed
     */
    public function next($date, $holidays = [], $days = null)
    {
        $valid = false;
        $dated = 0;

        if (self::isDate($date)) {
            do {
                $peaces = explode('-', $date);
                $day = $peaces[2] . '-' . $peaces[1];
                $is_weekend = in_array(date('w', strtotime($date)), ['0', '6']);
                $is_holiday = in_array($day, $holidays);
                $is_dated = $days === null ? true : ($dated >= (int)$days);

                if (!$is_weekend && !$is_holiday && $is_dated) {
                    $valid = true;
                } else {
                    /*
                    echo "$date, $dated, $days";
                    echo "<br>";
                    echo " W: " . ($is_weekend ? 'yes' : 'no');
                    echo " H: " . ($is_holiday ? 'yes' : 'no');
                    echo " D: " . ($is_dated ? 'yes' : 'no');
                    */
                    $date = self::add($date, 1);
                    if (!$is_weekend && !$is_holiday) {
                        $dated++;
                    }
                }
                //echo "<hr>";
            } while (!$valid);
        }

        return $date;
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
        $this->add(new \DateInterval("P{$days}D"));

        return $this->format($this->format);
    }
}
