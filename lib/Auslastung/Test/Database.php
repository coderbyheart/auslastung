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
 * Testrunner
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Database.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */

/**
 * Testrunner
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Database.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
abstract class Auslastung_Test_Database extends PHPUnit_Framework_TestCase
{
    /**
     * @var Auslastung_App_Test
     */
    protected $app;

    public function setUp()
    {
        $this->app = new Auslastung_App_Test();
    }

    protected function initDb()
    {
        $DoctrineManager = Doctrine_Manager::getInstance();
        $connection = $DoctrineManager->getCurrentConnection();

        $db = '`' . $connection->quoteIdentifier($connection->getDatabaseName()) . '`';

        $connection->exec('DROP DATABASE ' . $db);
        $connection->exec('CREATE DATABASE ' . $db);
        $connection->exec('USE ' . $db);

        $connection->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach (glob(dirname(__FILE__) . '/../../../sql/*.sql') as $sql) {
            $sqlCmd = file_get_contents($sql);
            // Use memory tables for testing
            $sqlCmd = str_replace('ENGINE=InnoDB', 'Engine=Memory', $sqlCmd);
            $sqlCmd = str_replace('name text', 'name varchar(255)', $sqlCmd);
            $sqlCmd = str_replace('description text', 'description varchar(255)', $sqlCmd);
            $sqlCmd = str_replace('tinytext', 'varchar(255)', $sqlCmd);
            $connection->exec($sqlCmd);
        }
        $connection->exec('SET FOREIGN_KEY_CHECKS=1');

        // Create Organization
        $Organization = new Model_Organization;
        $Organization->name = 'Unit used for PHPUnit';
        $Organization->save();
    }

    protected function initHolidays()
    {
        // Insert Holiday on 1.1.2009
        $H = new Model_Holiday;
        $H->start = '2009-01-01 00:00:00';
        $H->end = '2009-01-02 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'New Years Eve';
        $H->save();

        // Insert two test holidays with 1 day sep
        $H = new Model_Holiday;
        $H->start = '2009-01-20 00:00:00';
        $H->end = '2009-01-21 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();
        $H = new Model_Holiday;
        $H->start = '2009-01-22 00:00:00';
        $H->end = '2009-01-23 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();

        // Insert two test holidays without day sep
        $H = new Model_Holiday;
        $H->start = '2009-01-26 00:00:00';
        $H->end = '2009-01-27 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();
        $H = new Model_Holiday;
        $H->start = '2009-01-27 00:00:00';
        $H->end = '2009-01-28 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();

        // Insert two test holidays with a weekend sep
        $H = new Model_Holiday;
        $H->start = '2009-04-10 00:00:00';
        $H->end = '2009-04-11 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();
        $H = new Model_Holiday;
        $H->start = '2009-04-13 00:00:00';
        $H->end = '2009-04-14 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();

        // Insert three test holidays with a weekend sep and a day sep
        $H = new Model_Holiday;
        $H->start = '2009-05-15 00:00:00';
        $H->end = '2009-05-16 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();
        $H = new Model_Holiday;
        $H->start = '2009-05-18 00:00:00';
        $H->end = '2009-05-19 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();
        $H = new Model_Holiday;
        $H->start = '2009-05-20 00:00:00';
        $H->end = '2009-05-21 00:00:00';
        $H->is_holiday = true;
        $H->organization = 1;
        $H->description = 'Test holiday';
        $H->save();

        $this->assertEquals(10, count(Doctrine_Query::create()->from('Model_Holiday')->execute()->toArray()));
    }

    protected function initPersonData()
    {
        // Create Project
        $Project = new Model_Project;
        $Project->organization = 1;
        $Project->name = 'Test-Project';
        $Project->save();

        // Create Unit
        $Unit = new Model_Unit;
        $Unit->organization = 1;
        $Unit->name = 'Test-Unit';
        $Unit->save();

        // Create Discipline
        $Discipline = new Model_Discipline;
        // $Discipline->organization = 1;
        $Discipline->name = 'Test-Disziplin';
        $Discipline->save();

        // Create Propability
        $Probability = new Model_Probability;
        $Probability->organization = 1;
        $Probability->color = 'FFFFFF';
        $Probability->name = 'Test-Propability';
        $Probability->save();

        // Create VacationTyoe
        $VacationType = new Model_VacationType;
        $VacationType->organization = 1;
        $VacationType->name = 'Test-Vacation-Type';
        $VacationType->color = 'FFFFFF';
        $VacationType->save();

        // Create Person
        $Person = new Model_Person;
        $Person->organization = 1;
        $Person->unit = $Unit->id;
        $Person->discipline = $Discipline->id;
        $Person->name = 'Test Person';
        $Person->save();

        // Create User
        $User = new Model_User;
        $User->name = 'Test User';
        $User->email = 'test@domain.invalid';
        $User->save();

        $TestSession = new Auslastung_Test_Session('phpunit');
        $TestSession->setUser($User);
        Auslastung_Session::setInstance($TestSession);
    }
}