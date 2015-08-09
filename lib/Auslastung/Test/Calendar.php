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
 * Test class for Auslastung_Calendar
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Calendar.php 139 2011-01-02 18:29:47Z m $
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
 * Test class for Auslastung_Calendar
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Calendar.php 139 2011-01-02 18:29:47Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
class Auslastung_Test_Calendar extends Auslastung_Test_Database
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

	public function testGetJanuar()
	{
		$tests = array(
			array(
				'2009-01-01',
				array(
					'month' => '2009-01',
					'label' => 'Januar 2009',
					'next' => '2009-02-01',
					'previous' => '2008-12-01',
					'weekdays' => array(
						'Mo',
						'Di',
						'Mi',
						'Do',
						'Fr',
						'Sa',
						'So',
					),
					'weeks' => array(
						array(
							array(
								'label' => '29',
								'date' => '2008-12-29',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '30',
								'date' => '2008-12-30',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '31',
								'date' => '2008-12-31',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '1',
								'date' => '2009-01-01',
								'weekend' => false,
								'holiday' => true,
							),
							array(
								'label' => '2',
								'date' => '2009-01-02',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '3',
								'date' => '2009-01-03',
								'weekend' => true,
								'holiday' => false,
							),
							array(
								'label' => '4',
								'date' => '2009-01-04',
								'weekend' => true,
								'holiday' => false,
							),
						),
						array(
							array(
								'label' => '5',
								'date' => '2009-01-05',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '6',
								'date' => '2009-01-06',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '7',
								'date' => '2009-01-07',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '8',
								'date' => '2009-01-08',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '9',
								'date' => '2009-01-09',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '10',
								'date' => '2009-01-10',
								'weekend' => true,
								'holiday' => false,
							),
							array(
								'label' => '11',
								'date' => '2009-01-11',
								'weekend' => true,
								'holiday' => false,
							),
						),
						array(
							array(
								'label' => '12',
								'date' => '2009-01-12',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '13',
								'date' => '2009-01-13',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '14',
								'date' => '2009-01-14',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '15',
								'date' => '2009-01-15',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '16',
								'date' => '2009-01-16',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '17',
								'date' => '2009-01-17',
								'weekend' => true,
								'holiday' => false,
							),
							array(
								'label' => '18',
								'date' => '2009-01-18',
								'weekend' => true,
								'holiday' => false,
							),
						),
						array(
							array(
								'label' => '19',
								'date' => '2009-01-19',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '20',
								'date' => '2009-01-20',
								'weekend' => false,
								'holiday' => true,
							),
							array(
								'label' => '21',
								'date' => '2009-01-21',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '22',
								'date' => '2009-01-22',
								'weekend' => false,
								'holiday' => true,
							),
							array(
								'label' => '23',
								'date' => '2009-01-23',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '24',
								'date' => '2009-01-24',
								'weekend' => true,
								'holiday' => false,
							),
							array(
								'label' => '25',
								'date' => '2009-01-25',
								'weekend' => true,
								'holiday' => false,
							),
						),
						array(
							array(
								'label' => '26',
								'date' => '2009-01-26',
								'weekend' => false,
								'holiday' => true,
							),
							array(
								'label' => '27',
								'date' => '2009-01-27',
								'weekend' => false,
								'holiday' => true,
							),
							array(
								'label' => '28',
								'date' => '2009-01-28',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '29',
								'date' => '2009-01-29',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '30',
								'date' => '2009-01-30',
								'weekend' => false,
								'holiday' => false,
							),
							array(
								'label' => '31',
								'date' => '2009-01-31',
								'weekend' => true,
								'holiday' => false,
							),
							array(
								'label' => '1',
								'date' => '2009-02-01',
								'weekend' => true,
								'holiday' => false,
							),
						),
					),
				),
			),
		);
		foreach( $tests as $test ) {
			list($start, $result) = $test;
			// Test via PHP
			$Calendar = new Auslastung_Calendar(new DateTime($start));
			$this->assertEquals($result, $Calendar->getMonth());
			// Test via API
			$apiResult = json_decode(file_get_contents($this->app->getConfig()->getHref('api/calendar?start=' . $start . '&usetestdb=1')));
			$this->assertEquals($result['next'], $apiResult->result->next);
			$this->assertEquals($result['previous'], $apiResult->result->previous);
			$this->assertEquals($result['label'], $apiResult->result->label);
			$this->assertEquals($result['weekdays'], $apiResult->result->weekdays);
		}
	}
}
