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
 * Controller for Auslastung_Calendar
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Calendar.php 34 2009-03-15 21:37:28Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Controller for Auslastung_Calendar
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Calendar.php 34 2009-03-15 21:37:28Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Calendar implements Auslastung_Controller_Base
{

	public function __construct( Auslastung_Request $Request )
	{
		$Calendar = new Auslastung_Calendar(new DateTime($Request->getInput('start')));
		$this->Response = new Auslastung_Response_JSON();
		$this->Response->setResult($Calendar->getMonth());
	}

	/**
	* Get response of controller
	* @return Auslastung_Response
	*/
	public function getResponse()
	{
		return $this->Response;
	}
}
