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
 * Test class for Testing Holidays
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Vacation.php 154 2011-02-27 17:03:20Z m $
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
 * Test class for Testing Holidays
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Vacation.php 154 2011-02-27 17:03:20Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
class Auslastung_Test_Holiday extends Auslastung_Test_Database
{
    public function setUp()
    {
        parent::setUp();
        $this->initDb();
        $this->initHolidays();
        $this->initPersonData();
    }

    /**
     * Länge ermitteln
     */
    public function testDuration()
    {
        $H = new Model_Holiday;
        $H->start = '2009-02-20 00:00:00';
        $H->end = '2009-02-21 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();

        $this->assertEquals(8, $H->duration);

        $H2 = new Model_Holiday;
        $H2->start = '2011-12-19 14:00:00';
        $H2->end = '2011-12-19 18:00:00';
        $H2->is_holiday = true;
        $H2->organization = 1;
        $H2->description = 'Ein halber Tag Frei.';
        $H2->save();

        $this->assertEquals(4, $H2->duration);
    }

    /**
     * Feiertage gehen nicht über mehrere Tage
     *
     * @expectedException Doctrine_Validator_Exception
     */
    public function testMultipleDays()
    {
        $H = new Model_Holiday;
        $H->start = '2009-02-20 00:00:00';
        $H->end = '2009-02-21 00:00:01';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();
    }

    /**
     * Halber freiter Tag am Anfang der Woche
     */
    public function testHalfHolidayStartOfWeek()
    {
        $H = new Model_Holiday;
        $H->start = '2011-12-19 14:00:00';
        $H->end = '2011-12-19 18:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Ein halber Tag Frei.';
        $H->save();

        // Create Assignment
        $Assignment = new Model_Assignment;
        $Assignment->person = 1;
        $Assignment->project = 1;
        $Assignment->probability = 1;
        $Assignment->start = '2011-12-19';
        $Assignment->duration = 8;
        $Assignment->save();

        $this->assertEquals('2011-12-20', $Assignment->end);
    }

    /**
     * Löschen eines Freien Tages
     */
    public function testHolidayDelete()
    {
        $H = new Model_Holiday;
        $H->start = '2011-12-19 14:00:00';
        $H->end = '2011-12-19 18:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Ein halber Tag Frei.';
        $H->save();

        // Create Assignment
        $Assignment = new Model_Assignment;
        $Assignment->person = 1;
        $Assignment->project = 1;
        $Assignment->probability = 1;
        $Assignment->start = '2011-12-19';
        $Assignment->duration = 8;
        $Assignment->save();

        // Delete Holiday
        $H->delete();

        // Check Assignment
        $Assignment = Doctrine_Query::create()
            ->from('Model_Assignment a')
            ->where('a.id = ?', array($Assignment->id))
            ->fetchOne();
        $this->assertEquals('2011-12-19', $Assignment->end);
    }

    /**
     * Halber freiter Tag am Anfang der Woche
     */
    public function testHalfHolidayMidOfWeek()
    {
        $H = new Model_Holiday;
        $H->start = '2011-12-20 14:00:00';
        $H->end = '2011-12-20 18:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Ein halber Tag Frei.';
        $H->save();

        // Create Assignment
        $Assignment = new Model_Assignment;
        $Assignment->person = 1;
        $Assignment->project = 1;
        $Assignment->probability = 1;
        $Assignment->start = '2011-12-19';
        $Assignment->duration = 16;
        $Assignment->save();

        $this->assertEquals('2011-12-21', $Assignment->end);
    }

    /**
     * Halber freiter Tag am Ende der Woche
     */
    public function testHalfHolidayEndOfWeek()
    {
        $H = new Model_Holiday;
        $H->start = '2011-12-16 14:00:00';
        $H->end = '2011-12-16 18:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Ein halber Tag Frei.';
        $H->save();

        // Create Assignment
        $Assignment = new Model_Assignment;
        $Assignment->person = 1;
        $Assignment->project = 1;
        $Assignment->probability = 1;
        $Assignment->start = '2011-12-15';
        $Assignment->duration = 16;
        $Assignment->save();

        $this->assertEquals('2011-12-19', $Assignment->end);
    }

    /**
     * Halber freiter Tag am Ende der Woche und freier Montag
     */
    public function testHalfHolidayEndOfWeekPlusHolidayAfterWeekend()
    {
        $H = new Model_Holiday;
        $H->start = '2011-12-16 14:00:00';
        $H->end = '2011-12-16 18:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Ein halber Tag Frei.';
        $H->save();

        $H = new Model_Holiday;
        $H->start = '2011-12-19 00:00:00';
        $H->end = '2011-12-20 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Ein ganzer Tag Frei.';
        $H->save();

        // Create Assignment
        $Assignment = new Model_Assignment;
        $Assignment->person = 1;
        $Assignment->project = 1;
        $Assignment->probability = 1;
        $Assignment->start = '2011-12-15';
        $Assignment->duration = 16;
        $Assignment->save();

        $this->assertEquals('2011-12-20', $Assignment->end);
    }


    /**
     * Urlaub und freier Tag sollen sich addieren
     */
    public function testHolidayWithVacation()
    {
        $H = new Model_Holiday;
        $H->start = '2011-12-19 00:00:00';
        $H->end = '2011-12-20 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Ein ganzer Tag Frei.';
        $H->save();

        // Create Vacation
        $Vacation = new Model_Vacation;
        $Vacation->person = 1;
        $Vacation->start = '2011-12-20';
        $Vacation->days = 1;
        $Vacation->type = 1;
        $Vacation->save();

        // Create Assignment
        $Assignment = new Model_Assignment;
        $Assignment->person = 1;
        $Assignment->project = 1;
        $Assignment->probability = 1;
        $Assignment->start = '2011-12-16';
        $Assignment->duration = 16;
        $Assignment->save();

        $this->assertEquals('2011-12-21', $Assignment->end);

        // Delete Vacation
        $Vacation->delete();

        // Check Assignment
        $Assignment = Doctrine_Query::create()
            ->from('Model_Assignment a')
            ->where('a.id = ?', array($Assignment->id))
            ->fetchOne();
        $this->assertEquals('2011-12-20', $Assignment->end);

        // Delete Holiday
        $H->delete();

        // Check Assignment
        $Assignment = Doctrine_Query::create()
            ->from('Model_Assignment a')
            ->where('a.id = ?', array($Assignment->id))
            ->fetchOne();
        $this->assertEquals('2011-12-19', $Assignment->end);

    }
}