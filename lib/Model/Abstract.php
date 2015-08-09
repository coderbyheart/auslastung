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
 * Abstract Model
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Abstract.php 79 2009-06-04 22:42:23Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Abstract Model
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Abstract.php 79 2009-06-04 22:42:23Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
abstract class Model_Abstract extends Doctrine_Record implements Model_Interface
{
	/**
	 * Return wheter this entry may be deleted by the user
	 * 
	 * @return bool
	 */
	public static function isDeleteAble()
	{
		return true;
	}
}