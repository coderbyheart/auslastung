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
 * Test class for Auslastung_DateHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: DateHelper.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */

/**
 * Include required files
 */
if (!class_exists('Auslastung_Autoloader', false)) {
    require_once dirname(__FILE__) . '/../../Auslastung/Autoloader.php';
    new Auslastung_Autoloader();
}

/**
 * Test class for Auslastung_DateHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: DateHelper.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
class Auslastung_Test_DateHelper extends Auslastung_Test_Database
{
    public function setUp()
    {
        parent::setUp();
        $this->initDb();
        $this->initHolidays();
    }

    /*

         Jan 2009
         Mo  Tu  We  Th  Fr Sa Su
         29  30  31  [1]  2  3  4
          5   6   7   8   9 10 11
         12  13  14  15  16 17 18
         19 [20] 21 [22] 23 24 25
        [26  27] 28  29  30 31  1

         Feb 2009
         Mo Tu We Th Fr Sa Su
         26 27 28 29 30 31  1
          2  3  4  5  6  7  8
          9 10 11 12 13 14 15
         16 17 18 19 20 21 22
         23 24 25 26 27 28  1

         */

    public function testGetEndDate()
    {
        $hpd = 8;
        $tests = array(
            array('2009-01-05', 0.5, '2009-01-05'),
            array('2009-01-05', 1, '2009-01-05'),
            array('2009-01-05', 4, '2009-01-05'),
            array('2009-01-05', 5.5, '2009-01-05'),
            array('2009-01-05', 7.999999999999, '2009-01-05'),
            array('2009-01-05', $hpd, '2009-01-05'),
            array('2009-01-05', 8.1, '2009-01-06'),
            array('2009-01-05', $hpd * 2, '2009-01-06'),
            array('2009-01-05', $hpd * 3, '2009-01-07'),
            array('2009-01-05', $hpd * 4, '2009-01-08'),
            array('2009-01-05', $hpd * 5, '2009-01-09'),
            array('2009-01-05', $hpd * 6, '2009-01-12'),
            array('2009-01-05', $hpd * 7, '2009-01-13'),
            array('2009-01-05', $hpd * 11, '2009-01-19'),
            array('2009-01-05', $hpd * 12, '2009-01-21'),
            array('2009-01-05', $hpd * 13, '2009-01-23'),
            array('2009-01-05', $hpd * 14, '2009-01-28'),
            array('2009-01-05', $hpd * 15, '2009-01-29'),
            array('2009-01-19', 35, '2009-01-29'),
            array('2008-12-29', $hpd * 42, '2009-03-03'),
            array('2009-04-07', $hpd * 5, '2009-04-15'),
        );
        foreach ($tests as $test) {
            list($start, $hours, $result) = $test;
            // Test via PHP
            $this->assertEquals($result, Auslastung_DateHelper::getEndDate(new DateTime($start), $hours)->format('Y-m-d'), $start . ' + ' . $hours);
            // Test via API
            $apiResult = json_decode(file_get_contents($this->app->getConfig()->getHref('api/datehelper/getEndDate?start=' . $start . '&hours=' . number_format($hours, 9, '.', '') . '&usetestdb=1')));
            $this->assertEquals($result, $apiResult->result);
        }
    }

    public function testGetHoursBetweenDates()
    {
        $hpd = 8;
        $tests = array(
            array('2009-01-05', '2009-01-05', $hpd),
            array('2009-01-05', '2009-01-06', 2 * $hpd),
            array('2009-01-05', '2009-01-09', 5 * $hpd),
            array('2009-01-28', '2009-03-02', 24 * $hpd),
            // With weekend
            array('2009-01-08', '2009-01-10', 2 * $hpd),
            array('2009-01-08', '2009-01-11', 2 * $hpd),
            array('2009-01-08', '2009-01-12', 3 * $hpd),
            array('2009-01-05', '2009-01-12', 6 * $hpd),
            array('2009-01-05', '2009-01-19', 11 * $hpd),
            // With holiday
            array('2008-12-29', '2009-01-04', 4 * $hpd),
            array('2008-12-30', '2009-01-04', 3 * $hpd),
            array('2008-12-31', '2009-01-04', 2 * $hpd),
            array('2009-01-16', '2009-01-20', 2 * $hpd),
            array('2009-01-16', '2009-01-21', 3 * $hpd),
            array('2009-01-16', '2009-01-22', 3 * $hpd),
            array('2009-01-16', '2009-01-23', 4 * $hpd),
            array('2009-01-16', '2009-01-24', 4 * $hpd),
            array('2009-01-23', '2009-01-28', 2 * $hpd),
            array('2008-12-30', '2009-02-04', 22 * $hpd),
            array('2009-04-07', '2009-04-15', 5 * $hpd),
        );

        foreach ($tests as $test) {
            list($start, $end, $result) = $test;
            // Test via PHP
            $this->assertEquals($result, Auslastung_DateHelper::getHoursBetweenDates(new DateTime($start), new DateTime($end)), $start . ' - ' . $end);
            // Test via API
            $apiResult = json_decode(file_get_contents($this->app->getConfig()->getHref('api/datehelper/getHoursBetweenDates?start=' . $start . '&end=' . $end . '&usetestdb=1')));
            $this->assertEquals($result, $apiResult->result);
        }
    }

    public function testGetWorkingDaysBetweenDates()
    {
        $tests = array(
            array('2009-01-05', '2009-01-05', 1),
            array('2009-01-05', '2009-01-06', 2),
            array('2009-01-05', '2009-01-09', 5),
            array('2009-01-28', '2009-03-02', 24),
            // With weekend
            array('2009-01-08', '2009-01-10', 2),
            array('2009-01-08', '2009-01-11', 2),
            array('2009-01-08', '2009-01-12', 3),
            array('2009-01-05', '2009-01-12', 6),
            array('2009-01-05', '2009-01-19', 11),
            // With holiday
            array('2008-12-29', '2009-01-04', 4),
            array('2008-12-30', '2009-01-04', 3),
            array('2008-12-31', '2009-01-04', 2),
            array('2009-01-16', '2009-01-20', 2),
            array('2009-01-16', '2009-01-21', 3),
            array('2009-01-16', '2009-01-22', 3),
            array('2009-01-16', '2009-01-23', 4),
            array('2009-01-16', '2009-01-24', 4),
            array('2009-01-23', '2009-01-28', 2),
            array('2008-12-30', '2009-02-04', 22),
            array('2009-04-07', '2009-04-15', 5),
        );

        foreach ($tests as $test) {
            list($start, $end, $result) = $test;
            // Test via PHP
            $this->assertEquals($result, Auslastung_DateHelper::getWorkingDaysBetweenDates(new DateTime($start), new DateTime($end)), $start . ' - ' . $end);
            // Test via API
            $apiResult = json_decode(file_get_contents($this->app->getConfig()->getHref('api/datehelper/getWorkingDaysBetweenDates?start=' . $start . '&end=' . $end . '&usetestdb=1')));
            $this->assertEquals($result, $apiResult->result);
        }
    }

    /**
     * Test für Auslastung_DateHelper::getHolidayHours();
     */
    public function testGetHolidayHours()
    {
        $H = new Model_Holiday;
        $H->start = '2009-02-20 00:00:00';
        $H->end = '2009-02-21 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();

        $this->assertEquals(8, Auslastung_DateHelper::getHolidayHours(new DateTime('2009-02-20 23:59:59')));

        $H2 = new Model_Holiday;
        $H2->start = '2011-12-19 14:00:00';
        $H2->end = '2011-12-19 18:00:00';
        $H2->is_holiday = true;
        $H2->organization = 1;
        $H2->description = 'Ein halber Tag Frei.';
        $H2->save();

        $this->assertEquals(4, Auslastung_DateHelper::getHolidayHours(new DateTime('2011-12-19 23:59:59')));
    }
}
