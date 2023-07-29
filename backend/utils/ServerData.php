<?php

namespace utils;

use api\Request;
use DateTime;

class ServerData
{
    /**
     * Returns Date with Y-m-d format, the TimeZone that will be used is the settled in config file
     * @param bool $fulldate
     * @return string
     */
    public static function getDate(bool $fulldate = false): string
    {
        global $configs;
        date_default_timezone_set($configs['app']['defaultTimeZone']);
        if ($fulldate) {
            return date('Y-m-d H:i:s');
        }
        return date('Y-m-d');
    }

    public static function getTimeStamp(): int
    {
        global $configs;
        date_default_timezone_set($configs['app']['defaultTimeZone']);
        return time() * 1000;
    }

    public static function getIp(): string
    {
        return ($_SERVER['HTTP_CLIENT_IP'] ?? isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

    public static function isDateValid($date, $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }


    /**
     * Duelves la diferencia entre dos horas en horas
     * @param string $time1 Hora inferior en formato de 24 horas
     * @param string $time2 Higer time in 24 houts froma
     * @return float Differences between two times in hours
     */
    public static function getHoursFromTimes(string $time1, string $time2): float
    {
        $time1 = strtotime($time1);
        $time2 = strtotime($time2);
        return round(abs($time2 - $time1) / 3600, 2);
    }

    /**
     * Send Older and Newer date, the method will return the difference days from the older date to newer
     * @param $older
     * @param $newer
     * @return int|null
     */
    public static function getDifferenceDates($older, $newer): ?int
    {
        try {
            $date1 = new DateTime($older);
            $date2 = new DateTime($newer);
            return $date1->diff($date2)->format('%r%a');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if time has 24 hours format
     * @param string $time
     * @return bool
     */
    public static function check24Time(string $time): bool
    {
        $parts = explode(':', $time);
        if (count($parts) !== 3) {
            return false;
        }
        $hours = $parts[0];
        $minutes = $parts[1];
        $seconds = $parts[2];
        return $hours <= 24 && $minutes <= 59 && $seconds <= 59;
    }

    /**
     * It will check if a date is valid
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function checkDate(string $date, string $format = 'Y-m-d'): bool
    {
        $date_time = DateTime::createFromFormat($format, $date);
        return $date_time && $date_time->format($format) === $date;
    }

    public static function convertDaysToYears($days): int
    {
        return (int)($days / 365);
    }

    public function checkemail($str): bool
    {
        return !!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str);
    }

}