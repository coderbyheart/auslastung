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
 * Test class for Testing Users
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
 * Test class for Testing Users
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Vacation.php 154 2011-02-27 17:03:20Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
class Auslastung_Test_User extends Auslastung_Test_Database
{
	public function setUp()
	{
		parent::setUp();
		$this->initDb();
		$this->initHolidays();
		$this->initPersonData();
	}

	public function testRegister()
	{
        $User = new Model_User;
        $User->name = 'Markus Tacker';
        $User->email = 'm@tacker.org';
        $User->save();
	}
}