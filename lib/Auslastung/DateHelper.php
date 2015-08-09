<?php

//  +--------------------------------------------------+
//  | Copyright (c) Markus Tacker                      |
//  | All rights reserved.                             |
//  +--------------------------------------------------+
//  | This source code is protected by international   |
//  | copyright law and may not be distributed without |
//  | written permission by                            |
//  |   Markus Tacker                                  |
//  |   Senefelderstr. 63                              |
//  |   63069 Offenbach                                |
//  |   web:   http://coderbyheart.de                  |
//  |   email: m@tacker.org                            |
//  +--------------------------------------------------+

/**
 * DateHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: DateHelper.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * DateHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: DateHelper.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_DateHelper
{
    public static function getHoursBetweenDates(DateTime $Start, DateTime $End, $hoursPerDay = null, $includeHolidays = null, $includeWeekends = null)
    {
        if ($hoursPerDay === null) $hoursPerDay = 8;
        if ($includeHolidays === null) $includeHolidays = false;
        if ($includeWeekends === null) $includeWeekends = false;
        return self::getWorkingDaysBetweenDates($Start, $End, $includeHolidays, $includeWeekends) * $hoursPerDay;
    }

    public static function getWorkingDaysBetweenDates(DateTime $Start, DateTime $End, $includeHolidays = null, $includeWeekends = null)
    {
        if ($includeHolidays === null) $includeHolidays = false;
        if ($includeWeekends === null) $includeWeekends = false;
        if ($Start > $End) throw new InvalidArgumentException('$Start must be prior to $End');
        if (!$includeWeekends && self::isWeekend($Start)) throw new InvalidArgumentException('$Start must not be a weekend.');
        if (!$includeHolidays && self::isHoliday($Start)) throw new InvalidArgumentException('$Start must not be a holiday.');
        $Start->setTime(0, 0, 0);
        $End->setTime(23, 59, 59);
        $CountDaysDate = clone($Start);
        $nDays = 0;
        while ($CountDaysDate <= $End) {
            $nDays++;
            do {
                $CountDaysDate->modify('+1 day');
            } while ((!$includeWeekends && self::isWeekend($CountDaysDate))
                || (!$includeHolidays && self::isHoliday($CountDaysDate)));
        }
        return $nDays;
    }

    public static function getEndDate(DateTime $Start, $hours, $hoursPerDay = null, $includeHolidays = null, $includeWeekends = null)
    {
        if ($hoursPerDay === null) $hoursPerDay = 8;
        if ($includeHolidays === null) $includeHolidays = false;
        if ($includeWeekends === null) $includeWeekends = false;
        $hours = (float)$hours;
        if ($hours <= 0) throw new InvalidArgumentException('$hours must greater than zero.');
        if (!$includeWeekends && self::isWeekend($Start)) throw new InvalidArgumentException('$Start must not be a weekend.');
        if (!$includeHolidays && self::isHoliday($Start)) throw new InvalidArgumentException('$Start must not be a holiday.');
        $End = clone($Start);
        $Start->setTime(0, 0, 0);
        $End->setTime(23, 59, 59);
        $days = ceil($hours / $hoursPerDay);
        for ($i = 1; $i < $days; $i++) {
            do {
                $End->modify('+1 day');
            } while ((!$includeWeekends && self::isWeekend($End))
                || (!$includeHolidays && self::isHoliday($End)));
        }
        return $End;
    }

    public static function isHoliday(DateTime $Date)
    {
        return self::getHolidayHours($Date) >= 8;
    }

    public static function getHolidayHours(DateTime $Date)
    {
        $End = clone $Date;
        $End->modify('+1 day');
        $result = Doctrine_Query::create()
            ->select('SUM(duration) AS hours')
            ->from('Model_Holiday')
            ->where('start >= ? AND end <= ? AND is_holiday = ?', array($Date->format('Y-m-d 00:00:00'), $End->format('Y-m-d 00:00:00'), '1'))
            ->fetchArray();
        return (int)$result[0]['hours'];
    }

    public static function isWeekend(DateTime $Date)
    {
        return ((int)$Date->format('w') < 1 || (int)$Date->format('w') > 5);
    }
}