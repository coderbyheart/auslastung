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
 * Base response class
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Base.php 2 2008-12-19 07:54:52Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Response
 */
 
/**
 * Base response class
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Base.php 2 2008-12-19 07:54:52Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Response
 */
interface Auslastung_Response_Base
{
	/**
	* Send response to client
	*/
	public function send(); 
}
