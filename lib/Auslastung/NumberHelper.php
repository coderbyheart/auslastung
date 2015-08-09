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
 * NumberHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: NumberHelper.php 51 2009-03-30 12:47:16Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * NumberHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: NumberHelper.php 51 2009-03-30 12:47:16Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_NumberHelper
{
	public static function getFloat($string)
	{
		if (preg_match('/^[0-9]+,[0-9]+/', $string)) $string = str_replace(',', '.', $string);
		return (float)$string;
	}
}