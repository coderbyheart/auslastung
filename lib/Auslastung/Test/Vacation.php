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
 * Test class for Testing Assignemnts
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
 * Test class for Testing Assignemnts
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Vacation.php 154 2011-02-27 17:03:20Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
class Auslastung_Test_Vacation extends Auslastung_Test_Database
{
	public function setUp()
	{
		parent::setUp();
		$this->initDb();
		$this->initHolidays();
		$this->initPersonData();
	}

	public function testVacationSimple()
	{
		// Create Vacation
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start = '2009-04-06';
		$Vacation->days = 1.5;
		$Vacation->type = 1;
		$Vacation->save();

		$this->assertEquals('2009-04-07', $Vacation->end);
		$this->assertEquals(12, $Vacation->duration);
	}

	public function testGetHoursOnDay()
	{
		// Create Vacation
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start = '2009-04-06';
		$Vacation->days = 2.5;
		$Vacation->type = 1;
		$Vacation->save();

		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-04-06')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(4, $Vacation->getHoursOnDay(new DateTime('2009-04-08')));
	}

	public function testGetHoursOnDay3()
	{
		// Create Vacation
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start = '2009-04-06';
		$Vacation->days = 3;
		$Vacation->type = 1;
		$Vacation->save();

		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-04-06')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-04-08')));
	}

	public function testGetHoursOnDayHalfDay()
	{
		// Create Vacation
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start = '2009-04-06';
		$Vacation->days = 0.5;
		$Vacation->type = 1;
		$Vacation->save();

		$this->assertEquals(4, $Vacation->getHoursOnDay(new DateTime('2009-04-06')));
	}

	/**
	 * Test Model_Vacation::getHoursOnDay() with a vacation
	 * from 1h to 28 hours, with two holidays an a weekend inbetween
	 *
	 * Set Vacation length via 'duration'
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testGetHoursOnDayExtended()
	{
		$maxDuration = 28; // 3.5
		for($i = 1; $i < $maxDuration; $i++) {
			// Create Vacation
			$Vacation = new Model_Vacation;
			$Vacation->person = 1;
			$Vacation->start  = '2009-05-13';
			$Vacation->duration  = $i;
			$Vacation->type = 1;
			$Vacation->save();
			$this->assertEquals($i, $Vacation->duration);
			$this->assertEquals($i / 8, $Vacation->days);

			$Vacation = Doctrine_Query::create()
				->from('Model_Vacation')
				->where('id = ?', array($Vacation->id))
				->fetchOne();

			$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-15')));
			$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-16')));
			$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-17')));
			$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-18')));
			$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-20')));
			$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-22')));

			$this->assertEquals(min(8, $i), $Vacation->getHoursOnDay(new DateTime('2009-05-13')));
			$this->assertEquals(min(8, max(0, $i - 8)), $Vacation->getHoursOnDay(new DateTime('2009-05-14')));
			$this->assertEquals(min(8, max(0, $i - 16)), $Vacation->getHoursOnDay(new DateTime('2009-05-19')));
			$this->assertEquals(min(4, max(0, $i - 24)), $Vacation->getHoursOnDay(new DateTime('2009-05-21')));

			$this->assertEquals($i, $Vacation->duration);
			$this->assertEquals($i / 8, $Vacation->days);
		}
		$this->assertEquals(28, $i);
	}

	/**
	 * Test Model_Vacation::getHoursOnDay() with a vacation of
	 * 1 day, with two holidays an a weekend inbetween
	 *
	 * Set vacation length via 'end'
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testGetHoursOnDayExtendedByEnd1Day()
	{
		// 1 day
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-05-13';
		$Vacation->end  = '2009-05-13';
		$Vacation->type = 1;
		$Vacation->save();

		$Vacation = Doctrine_Query::create()
			->from('Model_Vacation')
			->where('id = ?', array($Vacation->id))
			->fetchOne();

		$this->assertEquals(8, $Vacation->duration);
		$this->assertEquals(1, $Vacation->days);
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-13')));
	}

	/**
	 * Test Model_Vacation::getHoursOnDay() with a vacation of
	 * 2 day, with two holidays an a weekend inbetween
	 *
	 * Set vacation length via 'end'
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testGetHoursOnDayExtendedByEnd2Day()
	{
		// 2 day
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-05-13';
		$Vacation->end  = '2009-05-14';
		$Vacation->type = 1;
		$Vacation->save();

		$Vacation = Doctrine_Query::create()
			->from('Model_Vacation')
			->where('id = ?', array($Vacation->id))
			->fetchOne();

		$this->assertEquals(16, $Vacation->duration);
		$this->assertEquals(2, $Vacation->days);
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-13')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-14')));
	}

	/**
	 * Test Model_Vacation::getHoursOnDay() with a vacation of
	 * 3 day, with two holidays an a weekend inbetween
	 *
	 * Set vacation length via 'end'
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testGetHoursOnDayExtendedByEnd3Day()
	{
		// 3 day
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-05-13';
		$Vacation->end  = '2009-05-19';
		$Vacation->type = 1;
		$Vacation->save();

		$Vacation = Doctrine_Query::create()
			->from('Model_Vacation')
			->where('id = ?', array($Vacation->id))
			->fetchOne();

		$this->assertEquals(24, $Vacation->duration);
		$this->assertEquals(3, $Vacation->days);
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-13')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-14')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-15')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-16')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-17')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-18')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-19')));
	}

	/**
	 * Test Model_Vacation::getHoursOnDay() with a vacation of
	 * 4 day, with two holidays an a weekend inbetween
	 *
	 * Set vacation length via 'end'
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testGetHoursOnDayExtendedByEnd4Day()
	{
		// 4 day
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-05-13';
		$Vacation->end  = '2009-05-21';
		$Vacation->type = 1;
		$Vacation->save();

		$Vacation = Doctrine_Query::create()
			->from('Model_Vacation')
			->where('id = ?', array($Vacation->id))
			->fetchOne();

		$this->assertEquals(32, $Vacation->duration);
		$this->assertEquals(4, $Vacation->days);
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-13')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-14')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-15')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-16')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-17')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-18')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-19')));
		$this->assertEquals(0, $Vacation->getHoursOnDay(new DateTime('2009-05-20')));
		$this->assertEquals(8, $Vacation->getHoursOnDay(new DateTime('2009-05-21')));
	}

	public function testAssignmentExtendedByVacationBetween()
	{
		// Create Assignment
		$Assignment              = new Model_Assignment;
		$Assignment->person      = 1;
		$Assignment->project     = 1;
		$Assignment->probability = 1;
		$Assignment->start       = '2009-04-07';
		$Assignment->manual_end  = '2009-04-08';
		$Assignment->save();

		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-08')));
		$this->assertEquals('2009-04-08', $Assignment->end);

		// Create Vacation Between
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-04-08';
		$Vacation->duration = 4;
		$Vacation->type = 1;
		$Vacation->save();

		$Assignment = Doctrine_Query::create()
			->from('Model_Assignment a')
			->leftJoin('a.Days')
			->where('a.id = ?', array($Assignment->id))
			->fetchOne();

		$this->assertEquals('2009-04-09', $Assignment->end);
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-04-08')));
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-04-09')));
	}

	public function testAssignmentExtendedByVacationStartsBefore()
	{
		// Create Assignment
		$Assignment              = new Model_Assignment;
		$Assignment->person      = 1;
		$Assignment->project     = 1;
		$Assignment->probability = 1;
		$Assignment->start       = '2009-04-07';
		$Assignment->manual_end  = '2009-04-08';
		$Assignment->save();

		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-08')));
		$this->assertEquals('2009-04-08', $Assignment->end);

		// Create Vacation Between
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-04-06';
		$Vacation->duration = 12;
		$Vacation->type = 1;
		$Vacation->save();

		$Assignment = Doctrine_Query::create()
			->from('Model_Assignment a')
			->leftJoin('a.Days')
			->where('a.id = ?', array($Assignment->id))
			->fetchOne();

		$this->assertEquals('2009-04-09', $Assignment->end);
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-08')));
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-04-09')));
	}

	public function testAssignmentExtendedByVacationEndsAfter()
	{
		// Create Assignment
		$Assignment              = new Model_Assignment;
		$Assignment->person      = 1;
		$Assignment->project     = 1;
		$Assignment->probability = 1;
		$Assignment->start       = '2009-04-07';
		$Assignment->manual_end  = '2009-04-08';
		$Assignment->save();

		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-08')));
		$this->assertEquals('2009-04-08', $Assignment->end);

		// Create Vacation Between
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-04-08';
		$Vacation->duration = 12;
		$Vacation->type = 1;
		$Vacation->save();

		$Assignment = Doctrine_Query::create()
			->from('Model_Assignment a')
			->leftJoin('a.Days')
			->where('a.id = ?', array($Assignment->id))
			->fetchOne();

		$this->assertEquals('2009-04-14', $Assignment->end);
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-04-08')));
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-04-09')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-04-10'))); // Test holiday
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-04-11'))); // Weekend
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-04-12'))); // Weekend
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-04-13'))); // Test holiday
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-04-14')));
	}
	
	/**
	 * Test for the author of a vacation
	 * 
	 * @see http://auslastung.coderbyheart.de/trac/ticket/26
	 */
	public function testVacationAuthor()
	{
		// Create Vacation
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start = '2009-04-06';
		$Vacation->days = 1.5;
		$Vacation->type = 1;
		$Vacation->save();

		$this->assertEquals(1, $Vacation->author);
	}
}