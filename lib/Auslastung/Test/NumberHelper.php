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
 * Test class for Auslastung_NumberHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: NumberHelper.php 139 2011-01-02 18:29:47Z m $
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
 * Test class for Auslastung_NumberHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: NumberHelper.php 139 2011-01-02 18:29:47Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
class Auslastung_Test_NumberHelper extends PHPUnit_Framework_TestCase
{
	public function testGetFloat()
	{
		$tests = array(
			array('0,5', 0.5),
			array('1,0', 1),
			array('2', 2),
			array('0,75', 0.75),
			array('20', 20),
			array('0.5', 0.5),
			array('1.0', 1),
			array('2', 2),
			array('0.75', 0.75),
		);
		foreach( $tests as $test ) {
			list($number, $float) = $test;
			$this->assertEquals($float, Auslastung_NumberHelper::getFloat($number));
		}
	}
}
