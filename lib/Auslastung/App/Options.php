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
 * Options for class Auslastung
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Options.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * Options for class Auslastung
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Options.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_App_Options
{
	private $isTest = false;

	public function isTest($bool = null)
	{
		if ($bool === null) {
			return $this->isTest;
		} else {
			$this->isTest = (bool)$bool;
			return $this;
		}
	}

	public static function create()
	{
		$class = __CLASS__;
		return new $class();
	}
}