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
 * Logout controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Logout.php 75 2009-05-03 19:00:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Logout controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Logout.php 75 2009-05-03 19:00:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Logout implements Auslastung_Controller_Base
{
	public function __construct( Auslastung_Request $Request )
	{
		self::logout();
		$this->Response = new Auslastung_Response_JSON();
	}

	/**
	 * Get response of controller
	 * @return Auslastung_Response
	 */
	public function getResponse()
	{
		return $this->Response;
	}

	public static function logout()
	{
		$authSession = new ezcAuthenticationSession();
		$authSession->start();
		$authSession->destroy();
		Auslastung_Session::getInstance()->destroy();
	}
}
