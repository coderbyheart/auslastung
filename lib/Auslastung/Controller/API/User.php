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
 * User controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: User.php 166 2011-12-28 17:54:45Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * User controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: User.php 166 2011-12-28 17:54:45Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_User extends Auslastung_Controller_API_Base
{
	protected $model = 'Model_User';

	public function __construct(Auslastung_Request $Request)
	{
		$this->Request = $Request;
		$this->Response = new Auslastung_Response_JSON;

		switch($Request->getMethod()) {
		case 'POST':
			switch($Request->getParam('subressource')) {
			case 'config':
				$User = Auslastung_Session::getInstance();
				foreach( Model_UserConfig::getPublicDirectives() as $directive ) {
					$value = filter_input(INPUT_POST, $directive, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
					if ($value !== null) $User->setUserConfig($directive, $value);
				}
				$config = $User->getUserConfig();
				$this->Response->setResult($config, count($config));
				break;
			default:
				parent::__construct($Request);
			}
			break;
		default:
			parent::__construct($Request);
		}
	}

	protected function addEntry()
	{
		$User = new Model_User;
		$User->name = $this->Request->getInput('name');
		$User->email = $this->Request->getInput('email');
		$User->save();
		$this->Response->setResult( array(
            'email' => $User->email,
            'name' => $User->name,
        ), 1 );
	}

	protected function updateEntry()
	{
		$Session = Auslastung_Session::getInstance();
		$User = Doctrine::getTable('Model_User')->find($Session->getUserId());
		$User->name = $this->Request->getInput('name');
		$User->email = $this->Request->getInput('email');
		$User->save();
		$Session->setUser($User);
		$this->Response->setResult(self::buildUserInfo(), 1);
	}

	public static function buildUserInfo()
	{
		$Session = Auslastung_Session::getInstance();
		return array('id' => $Session->getUserId(), 'name' => $Session->getUserName(), 'email' => $Session->getUserEmail(), 'config' => $Session->getUserConfig(), 'organization' => $Session->getOrganization());
	}
}
