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
 * Model for Users
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: User.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Users
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: User.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_User extends Model_Abstract
{
	private $createdPassword;
    public function setTableDefinition()
    {
        $this->setTableName('user');
        $this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('email', 'string', 255, array('type' => 'string', 'length' => 255));
        $this->hasColumn('password', 'string', 255, array('type' => 'string', 'length' => 255));
        $this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
        $this->hasColumn('is_active', 'enum', 1, array('type' => 'enum', 'length' => 1, 'values' => array(0 => '0', 1 => '1'), 'default' => '0'));
        $this->hasColumn('z_ts_created', 'timestamp', null, array('type' => 'timestamp'));
        $this->hasColumn('z_ts_modified', 'timestamp', null, array('type' => 'timestamp'));
        $this->hasColumn('z_ts_last_login', 'timestamp', null, array('type' => 'timestamp'));
    }

    public function setUp()
	{
		$this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
		$this->hasMany('Model_Organization as Organizations', array('local' => 'user', 'foreign' => 'organization', 'refClass' => 'Model_XUserOrganization'));
		$this->hasMany('Model_UserConfig as Config', array('local' => 'id', 'foreign' => 'user'));
	}

	protected function validate()
    {
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) $this->getErrorStack()->add('email', 'format');
		if (empty($this->id)) if (Doctrine_Query::create()->from('Model_User')->where('email = ?', $this->email)->fetchOne()) $this->getErrorStack()->add('email', 'duplicate');
	}

	public static function getForm($id = null, $type = null)
	{
		$User = Auslastung_Session::getInstance();
		if ((int)$id !== (int)$User->getUserId()) throw new Auslastung_Exception_Permission('Permission denied.');
		switch ($type) {
		case 'profile':
			$return = array(
				new Auslastung_FormField(array(
					'name' => 'name',
					'type' => 'text',
					'label' => 'Vor- und Nachname',
					'mandatory' => true,
					'value' => $User->getUserName(),
				)),
				new Auslastung_FormField(array(
					'name' => 'email',
					'type' => 'text',
					'label' => 'E-Mail-Adresse',
					'mandatory' => true,
					'value' => $User->getUserEmail(),
				)),
			);
			break;
		case 'register':
			$return = array(
				new Auslastung_FormField(array(
					'name' => 'name',
					'type' => 'text',
					'label' => 'Vor- und Nachname',
					'mandatory' => true,
				)),
				new Auslastung_FormField(array(
					'name' => 'email',
					'type' => 'text',
					'label' => 'E-Mail-Adresse',
					'mandatory' => true,
				)),
			);
			break;
		case 'login':
			$return = array(
				new Auslastung_FormField(array(
					'name' => 'email',
					'type' => 'text',
					'label' => 'E-Mail-Adresse',
					'mandatory' => true,
				)),
				new Auslastung_FormField(array(
					'name' => 'password',
					'type' => 'password',
					'label' => 'Passwort',
					'mandatory' => true,
				)),
			);
			break;
		case 'lostpassword':
			$return = array(
				new Auslastung_FormField(array(
					'name' => 'email',
					'type' => 'text',
					'label' => 'E-Mail-Adresse',
					'mandatory' => true,
				)),
			);
			break;
		}
		return $return;
	}

	public function preInsert($event)
	{
		$this->z_ts_created = Auslastung_Environment::getTimestamp();
		$this->generatePassword();
		$this->password = self::getPasswordHash($this->createdPassword);
		$this->is_active = '1';
	}

	private function generatePassword()
	{
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$this->createdPassword = '';
		for ($i = 0; $i < 8; $i++) $this->createdPassword .= substr($chars, rand(0, strlen($chars) - 1), 1);
	}

	public function postInsert($event)
	{
		$this->sendNewPasswordEmail();
	}

	public function sendNewPasswordEmail()
	{
		$body = array();
		$body[] = 'Deine Zugangsdaten:';
		$body[] = '';
		$body[] = 'Login: ' . $this->email;
		$body[] = 'Passwort: ' . $this->createdPassword;

		$Mailer = new Auslastung_Mailer();
		$Mailer->addTo($this);
		$Mailer->setSubject('Deine Zugangsdaten');
		$Mailer->setBody(join("\n", $body));
		$Mailer->send();
	}

	public function generateNewPassword()
	{
		$this->generatePassword();
		$this->password = self::getPasswordHash($this->createdPassword);
		$this->save();
		$this->sendNewPasswordEmail();
	}

	public static function getPasswordHash($password)
	{
		return '{SHA}' . base64_encode(sha1(trim($password), true));
	}

	/**
	 * Return wheter this entry may be deleted by the user
	 *
	 * @return bool
	 */
	public static function isDeleteAble()
	{
		return false;
	}

	/**
	 * Create a new user by email address
	 *
	 * @param string email
	 * @return Model_User
	 */
 	public static function createByEmail($email)
 	{
 		$class = __CLASS__;
 		$User = new $class;
	 	$User->name = ucwords(preg_replace('/[_\.+-]/', ' ', preg_replace('/@.+/', '', $email)));
 		$User->email = $email;
 		$User->save();
 		return $User;
 	}
}