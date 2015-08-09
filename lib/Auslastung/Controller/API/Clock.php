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
 * Clock controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Clock.php 2 2008-12-19 07:54:52Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
 
/**
 * Clock controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Clock.php 2 2008-12-19 07:54:52Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Clock implements Auslastung_Controller_Base
{
	/**
	* Get response of controller
	* @return Auslastung_Response
	*/
	public function getResponse()
	{
		$Response = new Auslastung_Response_JSON();
		$Response->setResult( strftime( '%d. %B %Y %H:%M' ) );
		return $Response;
	}
}
