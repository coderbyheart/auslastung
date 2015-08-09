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
 * Lostpassword controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Lostpassword.php 69 2009-05-03 14:55:05Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Lostpassword controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Lostpassword.php 69 2009-05-03 14:55:05Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Lostpassword implements Auslastung_Controller_Base
{
	public function __construct( Auslastung_Request $Request )
	{
		$this->Response = new Auslastung_Response_JSON();
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
		if ($User = Doctrine_Query::create()
			->from('Model_User')
			->where('email = ?', $email)
			->fetchOne()) {
			$User->generateNewPassword();
		} else {
			$this->Response->setStatus(Auslastung_Response_JSON::STATUS_FAILED, 2, 'Unbekannte E-Mail-Addresse: ' . $email, Auslastung_Response_JSON::USER_FAIL);
		}
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
