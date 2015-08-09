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
 * @version $Id: Assignment.php 154 2011-02-27 17:03:20Z m $
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
 * @version $Id: Assignment.php 154 2011-02-27 17:03:20Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
class Auslastung_Test_Assignment extends Auslastung_Test_Database
{
	public function setUp()
	{
		parent::setUp();
		$this->initDb();
		$this->initHolidays();
		$this->initPersonData();
	}

	public function testAssignmentSimple()
	{
		// Create Assignment
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start = '2009-04-06';
		$Assignment->duration = 12;
		$Assignment->save();

		$this->assertEquals('2009-04-07', $Assignment->end);
		$this->assertEquals(12, $Assignment->duration);
	}

	public function testAssignmentOverWeekend()
	{
		// Create Assignment
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start = '2009-04-02';
		$Assignment->duration = 32;
		$Assignment->save();

		$this->assertEquals('2009-04-07', $Assignment->end);
		$this->assertEquals(32, $Assignment->duration);
	}

	public function testAssignmentOverWeekendWithHolidays()
	{
		// Create Assignment
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start = '2009-04-07';
		$Assignment->duration = 40;
		$Assignment->save();

		$this->assertEquals('2009-04-15', $Assignment->end);
		$this->assertEquals(40, $Assignment->duration);
	}

	public function testAssignmentProlongedByPersonHoliday()
	{
		// Create Vacation
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start = '2009-04-07';
		$Vacation->days = 5;
		$Vacation->type = 1;
		$Vacation->save();

		$this->assertEquals('2009-04-15', $Vacation->end);
		$this->assertEquals(40, $Vacation->duration);

		// Create Assignment
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start = '2009-04-06';
		$Assignment->duration = 12;
		$Assignment->save();

		$this->assertEquals('2009-04-16', $Assignment->end);
		$this->assertEquals(12, $Assignment->duration);
	}

	public function testSimple()
	{
		// Create Vacation
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start = '2009-04-06';
		$Assignment->duration = 20;
		$Assignment->save();

		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-06')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-04-08')));
	}

	public function testGetHoursOnDayHalfDay()
	{
		// Create Vacation
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start = '2009-04-06';
		$Assignment->duration = 4;
		$Assignment->save();

		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-04-06')));
	}

	public function testManualEndDate()
	{
		// Create Vacation
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start = '2009-04-06';
		$Assignment->end = '2009-04-08';
		$Assignment->duration = 24;
		$Assignment->save();

		$this->assertEquals('2009-04-08', $Assignment->end);
		$this->assertEquals(24, $Assignment->duration);
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-06')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-07')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-04-08')));
	}

	/**
	 * Test Model_Assignment::getHoursOnDay()
	 * from 1h to 28 hours, with two holidays an a weekend inbetween
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
			// Create Assignment
			$Assignment = new Model_Assignment;
			$Assignment->person = 1;
			$Assignment->project = 1;
			$Assignment->probability = 1;
			$Assignment->start  = '2009-05-13';
			$duration = $maxDuration - $i;
			$Assignment->duration = $duration;
			$Assignment->save();

			$Assignment = Doctrine_Query::create()
				->from('Model_Assignment')
				->where('id = ?', array($Assignment->id))
				->fetchOne();

			$this->assertEquals($duration, $Assignment->duration);
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-15')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-16')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-17')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-18')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-20')));

			if($duration < 9) {
				$this->assertEquals('2009-05-13', $Assignment->end, $duration);
				$this->assertEquals($duration, $Assignment->getHoursOnDay(new DateTime('2009-05-13')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-14')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-19')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-21')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-22')));
			} else if($duration < 17) {
				$this->assertEquals('2009-05-14', $Assignment->end, 'Duration: ' . $duration);
				$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-13')));
				$this->assertEquals($duration - 8, $Assignment->getHoursOnDay(new DateTime('2009-05-14')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-15')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-19')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-21')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-22')));
			} else if($duration < 25) {
				$this->assertEquals('2009-05-19', $Assignment->end, 'Duration: ' . $duration);
				$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-13')));
				$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-14')));
				$this->assertEquals($duration - 16, $Assignment->getHoursOnDay(new DateTime('2009-05-19')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-21')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-22')));
			} else {
				$this->assertEquals('2009-05-21', $Assignment->end, 'Duration: ' . $duration);
				$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-13')));
				$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-14')));
				$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-19')));
				$this->assertEquals($duration - 24, $Assignment->getHoursOnDay(new DateTime('2009-05-21')));
				$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-22')));
			}
		}
		$this->assertEquals(28, $i);
	}

	/**
	 * Test Model_Assignment::getHoursOnDay() with a vacation
	 * from 1h to 28 hours, with two holidays an a weekend inbetween
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testGetHoursOnDayExtendedVacation()
	{
		$maxDuration = 28; // 3.5
		for($vDuration = 1; $vDuration < $maxDuration; $vDuration++) {

			$aDuration = $maxDuration - $vDuration;

// 			echo "-----------------------\n";
// 			echo "Assignment: $aDuration\n";
// 			echo "Vacation:   $vDuration\n";

			Doctrine_Query::create()
				->from('Model_Vacation')
				->delete()
				->execute();

			// Create Vacation
			$Vacation = new Model_Vacation;
			$Vacation->person = 1;
			$Vacation->start  = '2009-05-13';
			$Vacation->duration  = $vDuration;
			$Vacation->type = 1;
			$Vacation->save();
			$this->assertEquals($vDuration, $Vacation->duration);
			$this->assertEquals($vDuration / 8, $Vacation->days);

			// Create Assignment
			$Assignment = new Model_Assignment;
			$Assignment->person = 1;
			$Assignment->project = 1;
			$Assignment->probability = 1;
			$Assignment->start  = '2009-05-13';
			$Assignment->duration = $aDuration;
			$Assignment->save();

			$Assignment = Doctrine_Query::create()
				->from('Model_Assignment')
				->where('id = ?', array($Assignment->id))
				->fetchOne();

			$this->assertEquals($aDuration, $Assignment->duration);

			// Always 0
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-15')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-16')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-17')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-18')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-20')));

			$this->assertEquals(max(0, 8 - $vDuration), $Assignment->getHoursOnDay(new DateTime('2009-05-13')), 'Duration: ' . $aDuration . ' Vacation: ' . $vDuration);
			$this->assertEquals(max(0, min(8, 16 - $vDuration)), $Assignment->getHoursOnDay(new DateTime('2009-05-14')), 'Duration: ' . $aDuration . ' Vacation: ' . $vDuration);
			$this->assertEquals(max(0, min(8, 24 - $vDuration)), $Assignment->getHoursOnDay(new DateTime('2009-05-19')), 'Duration: ' . $aDuration . ' Vacation: ' . $vDuration);
			$this->assertEquals(max(0, min(4, 28 - $vDuration)), $Assignment->getHoursOnDay(new DateTime('2009-05-21')), 'Duration: ' . $aDuration . ' Vacation: ' . $vDuration);
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-22')), 'Duration: ' . $aDuration . ' Vacation: ' . $vDuration);
		}
		$this->assertEquals(28, $vDuration);
	}

	/**
	 * Special Test for 14h Assignment with 14h Vacation
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testGetHoursOnDayExtendedVacation1414()
	{
		$aduration = 14;
		$vduration = $aduration;

		// Create Vacation
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-05-13';
		$Vacation->type = 1;
		$Vacation->duration  = $vduration;
		$Vacation->save();

		// Create Assignment
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start  = '2009-05-13';
		$Assignment->duration = $aduration;
		$Assignment->save();

		$Assignment = Doctrine_Query::create()
			->from('Model_Assignment')
			->where('id = ?', array($Assignment->id))
			->fetchOne();

		$this->assertEquals($aduration, $Assignment->duration);
		$this->assertEquals('2009-05-21', $Assignment->end);
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-13')));
		$this->assertEquals(2, $Assignment->getHoursOnDay(new DateTime('2009-05-14')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-15')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-16')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-17')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-18')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-19')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-20')));
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-05-21')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-22')));
	}

	/**
	 *Special Test for 26h Assignment with 2h Vacation
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testGetHoursOnDayExtendedVacation262()
	{
		$aduration = 26;
		$vduration = 2;

		// Create Vacation
		$Vacation = new Model_Vacation;
		$Vacation->person = 1;
		$Vacation->start  = '2009-05-13';
		$Vacation->duration  = $vduration;
		$Vacation->type = 1;
		$Vacation->save();

		// Create Assignment
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start  = '2009-05-13';
		$Assignment->duration = $aduration;
		$Assignment->save();

		$Assignment = Doctrine_Query::create()
			->from('Model_Assignment')
			->where('id = ?', array($Assignment->id))
			->fetchOne();

		$this->assertEquals($aduration, $Assignment->duration);
		$this->assertEquals('2009-05-21', $Assignment->end);
		$this->assertEquals(6, $Assignment->getHoursOnDay(new DateTime('2009-05-13')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-14')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-15')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-16')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-17')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-18')));
		$this->assertEquals(8, $Assignment->getHoursOnDay(new DateTime('2009-05-19')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-20')));
		$this->assertEquals(4, $Assignment->getHoursOnDay(new DateTime('2009-05-21')));
		$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-22')));
	}

	/**
	 * Test for manual_end
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testManualEnd()
	{
		for($i = 1; $i <= 28; $i++) {
			Doctrine_Query::create()
				->from('Model_Vacation')
				->delete()
				->execute();

			Doctrine_Query::create()
				->from('Model_Assignment')
				->delete()
				->execute();

			// Create Vacation
			$Vacation = new Model_Vacation;
			$Vacation->person   = 1;
			$Vacation->start    = '2009-05-13';
			$Vacation->duration = $i;
			$Vacation->type = 1;
			$Vacation->save();

			// Create Assignment
			$Assignment              = new Model_Assignment;
			$Assignment->person      = 1;
			$Assignment->project     = 1;
			$Assignment->probability = 1;
			$Assignment->start       = '2009-05-13';
			$Assignment->manual_end  = '2009-05-21';
			$Assignment->save();

			$Assignment = Doctrine_Query::create()
				->from('Model_Assignment')
				->where('id = ?', array($Assignment->id))
				->fetchOne();

			$this->assertEquals(32 - $i, $Assignment->duration);
			$this->assertEquals('2009-05-21', $Assignment->end);

			$this->assertEquals(max(0, 8 - $i), $Assignment->getHoursOnDay(new DateTime('2009-05-13')), 'Vacation: ' . $i);
			$this->assertEquals(min(8, max(0, 16 - $i)), $Assignment->getHoursOnDay(new DateTime('2009-05-14')), 'Vacation: ' . $i);
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-15')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-16')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-17')));
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-18')));
			$this->assertEquals(min(8, max(0, 24 - $i)), $Assignment->getHoursOnDay(new DateTime('2009-05-19')), 'Vacation: ' . $i);
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-20')));
			$this->assertEquals(min(8, max(0, 32 - $i)), $Assignment->getHoursOnDay(new DateTime('2009-05-21')), 'Vacation: ' . $i);
			$this->assertEquals(0, $Assignment->getHoursOnDay(new DateTime('2009-05-22')));
		}
	}

	/**
	 * Test for manual_end with duration
	 *
	 * May 2009
	 *  Mo  Di  Mi  Do  Fr  Sa So
	 *  11  12  13  14 [15] 16 17
	 * [18] 19 [20] 21  22
	 */
	public function testManualEndWithDuration()
	{
		// Create Assignment
		$Assignment                      = new Model_Assignment;
		$Assignment->person              = 1;
		$Assignment->project             = 1;
		$Assignment->probability         = 1;
		$Assignment->start               = '2009-05-11';
		$Assignment->manual_end          = '2009-05-14';
		$Assignment->duration            = 8;
		$Assignment->distribute_duration = 1;
		$Assignment->save();

		$Assignment = Doctrine_Query::create()
			->from('Model_Assignment')
			->where('id = ?', array($Assignment->id))
			->fetchOne();

		$this->assertEquals(2, $Assignment->getHoursOnDay(new DateTime('2009-05-11')));
		$this->assertEquals(2, $Assignment->getHoursOnDay(new DateTime('2009-05-12')));
		$this->assertEquals(2, $Assignment->getHoursOnDay(new DateTime('2009-05-13')));
		$this->assertEquals(2, $Assignment->getHoursOnDay(new DateTime('2009-05-14')));
	}
	
	/**
	 * Test for the author of an assigment
	 * 
	 * @see http://auslastung.coderbyheart.de/trac/ticket/26
	 */
	public function testAssignmentAuthor()
	{
		// Create Assignment
		$Assignment = new Model_Assignment;
		$Assignment->person = 1;
		$Assignment->project = 1;
		$Assignment->probability = 1;
		$Assignment->start = '2009-04-06';
		$Assignment->duration = 12;
		$Assignment->save();

		$this->assertEquals(1, $Assignment->author);
	}
}