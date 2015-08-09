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
 * Application configuration for Unit Tests
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: TestConfig.php 149 2011-01-03 17:10:17Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * Application configuration for Unit Tests
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: TestConfig.php 149 2011-01-03 17:10:17Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_App_TestConfig extends Auslastung_App_Config
{
 	protected function loadConfig()
 	{
 		parent::loadConfig();
 		include $this->getFile('data/testconfig.php');
 	}
}