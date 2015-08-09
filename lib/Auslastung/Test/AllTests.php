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
 * @version $Id: AllTests.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */

// Force my own error reporting settings - they may have been changed by the includes above
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('log_errors', 0);
restore_error_handler();
restore_exception_handler();

require_once dirname(__FILE__) . '/../../Auslastung/Autoloader.php';
new Auslastung_Autoloader();
new Auslastung_App_Test();

/**
 * Testrunner
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: AllTests.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Test
 */
class Auslastung_Test_AllTests
{
    public static function suite()
    {
		$Suite = new PHPUnit_Framework_TestSuite('Testsuite');
		$Suite->addTestSuite('Auslastung_Test_DateHelper');
		$Suite->addTestSuite('Auslastung_Test_Calendar');
		$Suite->addTestSuite('Auslastung_Test_NumberHelper');
		$Suite->addTestSuite('Auslastung_Test_Assignment');
		$Suite->addTestSuite('Auslastung_Test_Vacation');
        $Suite->addTestSuite('Auslastung_Test_User');
        $Suite->addTestSuite('Auslastung_Test_Holiday');
		return $Suite;
    }
}