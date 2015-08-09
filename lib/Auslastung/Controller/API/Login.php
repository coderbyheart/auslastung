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
 * Login controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Login.php 184 2012-01-02 10:34:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Login controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Login.php 184 2012-01-02 10:34:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Login implements Auslastung_Controller_Base
{
	private $authSession;

	public function __construct( Auslastung_Request $Request )
	{
		$this->authSession = new ezcAuthenticationSession();
		$this->authSession->start();

		$this->Response = new Auslastung_Response_JSON();

		$this->handleLogin();
	}

	/**
	 * Get response of controller
	 * @return Auslastung_Response
	 */
	public function getResponse()
	{
		return $this->Response;
	}

	private function handleLogin()
	{
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if (empty($email)) $email = $this->authSession->load();
		$credentials = new ezcAuthenticationPasswordCredentials($email, empty($password) ? null : Model_User::getPasswordHash($password));
		$authFilter = new Auslastung_ezcAuthenticationFilter();

		$Auth = new ezcAuthentication($credentials);
		$Auth->session = $this->authSession;
		$Auth->addFilter($authFilter);

		if ($Auth->run() === false) {
			Auslastung_Controller_API_Logout::logout();
			$this->Response->setStatus(Auslastung_Response_JSON::STATUS_FAILED, 2, 'Login fehlgeschlagen', Auslastung_Response_JSON::USER_FAIL);
			$this->Response->setResult(array('email', 'password'), 2);
		} else {
			$User = $authFilter->getUser();
			$Session = Auslastung_Session::getInstance();
			if (!$Session->hasUser() && $User === null) {
                Auslastung_Session::getInstance()->destroy();
				return;
			}
			if (!$Session->hasUser()) $Session->setUser($User);
			$this->Response->setResult(Auslastung_Controller_API_User::buildUserInfo(), 1);
		}
	}
}