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
 * The session
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Session.php 184 2012-01-02 10:34:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Dispatcher
 */

/**
 * The session
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Session.php 184 2012-01-02 10:34:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Dispatcher
 */
class Auslastung_Session
{
	private $userConfigChangedKeys = array();
	private static $instance;
	
	public static function create($version)
	{
        self::setInstance(new Auslastung_Session($version));
		return self::$instance;
	}
	
	public static function getInstance()
	{
		if (self::$instance === null) throw new Auslastung_Exception_Session("No session.");
		return self::$instance;
	}

    public static function setInstance(Auslastung_Session $session)
    {
        self::$instance = $session;
    }


	private function __construct($version)
	{
		if (!isset($_SESSION[__CLASS__])
		|| $_SESSION[__CLASS__]['version'] !== $version) {
			$_SESSION[__CLASS__] = array(
				'User' => null,
				'userConfig' => null,
				'organizations' => null,
				'currentOrganization' => null,
				'version' => $version,
			);
		}
	}

	/**
	 * @retun bool
	 */
	public function hasUser()
	{
		return $_SESSION[__CLASS__]['User'] !== null;
	}

	/**
	 * @return int
	 */
	public function getUserId()
	{
		return (int)$_SESSION[__CLASS__]['User']['id'];
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $_SESSION[__CLASS__]['User']['name'];
	}

	/**
	 * @return string
	 */
	public function getUserEmail()
	{
		return $_SESSION[__CLASS__]['User']['email'];
	}

	/**
	 * @return array
	 */
	public function getOrganization()
	{
		if ($_SESSION[__CLASS__]['currentOrganization'] === null) return null;
		if (!isset($_SESSION[__CLASS__]['organizations'][$_SESSION[__CLASS__]['currentOrganization']])) {
			$_SESSION[__CLASS__]['currentOrganization'] = null;
			return null;
		}
		return $_SESSION[__CLASS__]['organizations'][$_SESSION[__CLASS__]['currentOrganization']];
	}

	/**
	 * @return int
	 */
	public function getOrganizationId()
	{
		if ($_SESSION[__CLASS__]['currentOrganization'] === null) return null;
		return (int)$_SESSION[__CLASS__]['currentOrganization'];
	}

	/**
	 * @return string
	 */
	public function getOrganizationName()
	{
		if ($_SESSION[__CLASS__]['currentOrganization'] === null) return null;
		$org = $this->getOrganization();
		return $org['name'];
	}

	/**
	 * @param string
	 * @return string
	 */
	public function getOrganizationConfig($key)
	{
		if ($_SESSION[__CLASS__]['currentOrganization'] === null) return null;
		$org = $this->getOrganization();
		return isset($org['config'][$key]) ? $org['config'][$key] : null;
	}

	public function setUser(Model_User $User)
	{
		$_SESSION[__CLASS__]['User'] = array(
			'id' => $User->id,
			'email' => $User->email,
			'name' => $User->name,
		);
		$_SESSION[__CLASS__]['userConfig'] = array();
		foreach ($User->Config as $Config) $_SESSION[__CLASS__]['userConfig'][$Config->name] = $Config->value;

		$_SESSION[__CLASS__]['organizations'] = array();
		foreach ($User->Organizations as $Org) {
			$this->updateOrganization($Org);
		}

		$_SESSION[__CLASS__]['currentOrganization'] = $this->getUserConfig(Model_UserConfig::CURRENT_ORGANIZATION);
		if ($_SESSION[__CLASS__]['currentOrganization'] === null) {
			$oids = array_keys($_SESSION[__CLASS__]['organizations']);
			if (empty($oids)) {
				// Create Organization
				$Org = new Model_Organization;
				$Org->name = $this->getUserName() . "'s Company";
				$Org->z_created_by = $User->id;
				$Org->save();
				$U2O = new Model_XUserOrganization;
				$U2O->organization = $Org->id;
				$U2O->user = $User->id;
				$U2O->save();
				$this->updateOrganization($Org);
				$_SESSION[__CLASS__]['currentOrganization'] = $Org->id;
			} else {
				$_SESSION[__CLASS__]['currentOrganization'] = $oids[0];
			}
			$this->setUserConfig(Model_UserConfig::CURRENT_ORGANIZATION, $_SESSION[__CLASS__]['currentOrganization']);
		}
	}

	/**
	 * @param string
	 * @return string
	 */
	public function getUserConfig($key = null)
	{
		if ($key === null) return $_SESSION[__CLASS__]['userConfig'];
		return isset($_SESSION[__CLASS__]['userConfig'][$key]) ? $_SESSION[__CLASS__]['userConfig'][$key] : null;
	}

	public function setUserConfig($key, $value)
	{
		if (!isset($_SESSION[__CLASS__]['userConfig'][$key])
		|| $_SESSION[__CLASS__]['userConfig'][$key] !== $value) {
			$_SESSION[__CLASS__]['userConfig'][$key] = $value;
			$this->userConfigChangedKeys[] = $key;
		}

	}

	public function __destruct()
	{
		if (!empty($this->userConfigChangedKeys)) {
			foreach ($this->userConfigChangedKeys as $key) {
				if ($UserConfig = Doctrine_Query::create()
					->from('Model_UserConfig')
					->where('name = ? AND user = ?', array($key, $this->getUserId()))
					->fetchOne()) {
					$UserConfig->value = $this->getUserConfig($key);
					$UserConfig->save();
				} else {
					$UserConfig = new Model_UserConfig;
					$UserConfig->user = $this->getUserId();
					$UserConfig->name = $key;
					$UserConfig->value = $this->getUserConfig($key);
					$UserConfig->save();
				}
			}
		}
	}

	public function destroy()
	{
		$_SESSION[__CLASS__] = null;
		unset($_SESSION[__CLASS__]);
        session_destroy();
	}

	public function updateOrganization(Model_Organization $Org)
	{
		$orgArray = array(
			'id' => $Org->id,
			'name' => $Org->name,
			'z_created_by' => $Org->z_created_by,
			'config' => array(),
		);
		foreach ($Org->Config as $Config) $orgArray['config'][$Config->name] = $Config->value;
		$_SESSION[__CLASS__]['organizations'][$Org->id] = $orgArray;
	}
}