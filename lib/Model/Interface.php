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
 * Interface for Models
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Interface.php 79 2009-06-04 22:42:23Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Interface for Models
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Interface.php 79 2009-06-04 22:42:23Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
interface Model_Interface
{
	/**
	 * Return wheter this entry may be deleted by the user
	 * 
	 * @return bool
	 */
	static function isDeleteAble();
}