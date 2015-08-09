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
 * Application configuration
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Config.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * Application configuration
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Config.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_App_Config
{
	/**
	 * @var string Developer Mode 
	 */
	const DEVELOPER = 'DEVELOPER';

	/**
	* @var bool We are in DEVELOPER mode?
	*/
	protected $developerSwitches = array();
	
	/**
	 * @var EngineRoom_FixedHashMap data
	 */
	private $data;

	/**
	 * Constructor
	 **/
 	public function __construct()
 	{
 		$this->data = new EngineRoom_FixedHashMap(array(
 			'cli',
 			'locale',
 			'tracUrl', 
 			'svnrev',
 			'dsn',		
 			'baseHref',
 			'name',
 			'home'
 		));
 		$this->detectSettings();
		$this->setDefaults();
		$this->loadConfig();
		$this->configurePhp();
 	}

 	protected function detectSettings()
 	{
 		$this->setHome(realpath(dirname(__FILE__).'/../../../') . '/');
 		$this->setCli(isset($_SERVER['PWD']));
 		@include $this->getFile('data/svnrev.php');
 		if ($this->getSvnRev() === null) $this->setSvnRev('unknown-version');
 		$this->detectDeveloperSwitches();
 	}

 	protected function setDefaults()
 	{
 		$this->setLocale('de_DE.utf8');
 		$this->setTracUrl('http://auslastung.coderbyheart.de/trac');
 		$this->setName("Auslastung");
 	}

 	protected function loadConfig()
 	{
 		require $this->getFile('data/config.php');
 	}

 	public function getFile($filename)
 	{
 		return $this->getHome() . $filename;
 	}

 	public function getDir($dir)
 	{
 		return $this->getHome() . $dir;
 	}

 	public function getVarDir($dir)
 	{
 		return $this->getHome() . 'var/' . $dir;
 	}

 	protected function detectDeveloperSwitches()
 	{
		// Error handling switches
		$switches = array(self::DEVELOPER);
		foreach ($switches as $switch) {
			if (isset($_SERVER['REQUEST_URI']) and stristr($_SERVER['REQUEST_URI'], $switch . 'ON')) {
				$this->developerSwitches[$switch] = true;
				setcookie($switch . 'ON', true, 0, '/');
			} else if (isset($_SERVER['REQUEST_URI']) and stristr($_SERVER['REQUEST_URI'], $switch . 'OFF')) {
				setcookie($switch . 'ON', false, 0, '/');
				$this->developerSwitches[$switch] = false;
			} else if (isset($_COOKIE[$switch . 'ON'])) {
				$this->developerSwitches[$switch] = true;
			} else {
				$this->developerSwitches[$switch] = false;
			}
		}
 	}

 	/**
 	 * @return bool if we are in developer mode
 	 */
 	public function isDeveloper()
 	{
 		return $this->developerSwitches[self::DEVELOPER];
 	}
 	
	/**
 	 * @param string Switch
 	 * @param boolean enable or disable
 	 */
 	public function setDeveloperSwitch($switch, $boolean)
 	{
 		$switches = array(self::DEVELOPER);
 		if (!in_array($switch, $switches)) throw new Auslastung_Exception_Config(sprintf("Unknown switch: %s", $switch));
 		$this->developerSwitches[$switch] = (bool)$boolean;
 	}

 	public function isCli()
 	{
 		return $this->data->cli;
 	}

 	/**
 	 * Locale setting
 	 * 
	 * Du bist Deutschland!
	 */
 	protected function setLocale($locale)
 	{
 		$this->data->locale = $locale;
		setlocale(LC_ALL, $locale);
 	}

 	protected function configurePhp()
 	{
		// Default error reporting: disable error output and log them
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR|E_RECOVERABLE_ERROR|E_PARSE);
		ini_set('display_errors', 0);
		ini_set('log_errors', true);
		ini_set('error_log', $this->getFile('var/log/php.log'));

		// Show errors if DEVELOPER mode is enabled or we are in CLI
		if ($this->isDeveloper() || $this->isCli()) {
			error_reporting(E_ALL|E_STRICT);
			ini_set('display_errors', 1);
			ini_set('log_errors', false);
		}
 	}
 	
	/**
	 * @param string SVN Revision
	 */
 	public function setSvnRev($svnrev)
 	{
 		$this->data->svnrev = $svnrev;
 	}
 	
	/**
	 * @return string SVN Revision
	 */
 	public function getSvnRev()
 	{
 		return $this->data->svnrev;
 	}

 	/**
 	 * @return string Trac URL
 	 */
 	public function getTracUrl()
 	{
 		return $this->data->tracUrl;
 	}
 	
 	/**
 	 * @param string Trac URL
 	 */
 	public function setTracUrl($tracUrl)
 	{
 		$this->data->tracUrl = $tracUrl;
 	}
 	
 	/**
 	 * @return string MySQL connection DSN i.e. mysql://user:pass@host/db
 	 */
 	public function getDSN()
 	{
 		return $this->data->dsn;
 	}
 	
 	/**
 	 * @param string MySQL connection DSN i.e. mysql://user:pass@host/db
 	 */
 	public function setDSN($dsn)
 	{
 		$this->data->dsn = $dsn;
 	}	

 	/**
 	 * @param string Base Host w/ Protocol i.e. http://auslastung.coderbyheart.de/
 	 */
 	public function setBaseHref($baseHref)
 	{
 		if (substr($baseHref, -1) !== '/') $baseHref .= '/';
		$this->data->baseHref = $baseHref;  		
 	}
 	
 	/**
 	 * Returns the Protocal and Hostname, i.e. http://auslastung.coderbyheart.de/
 	 */
 	public function getBaseHref()
 	{
		if ($this->data->baseHref === null) {
            if (!isset($_SERVER['HTTP_HOST'])) throw new Auslastung_Exception_Config('baseHref is not set and could not auto-determine it.');
            $this->data->baseHref = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/';
        }
		return $this->data->baseHref;  		
 	}
 	
	/**
 	 * Return a complete URL
 	 * 
 	 * @param string to add to basehref
 	 */
 	public function getHref($add)
 	{
		return $this->getBaseHref() . $add;  		
 	}
 	
 	/**
 	 * Sets the app's name
 	 * 
 	 * @param string $name
 	 */
 	public function setName($name)
 	{
 		$this->data->name = $name;
 	}
 	
 	/**
 	 * Returns the app's name
 	 * 
 	 * @return string
 	 */
 	public function getName()
 	{
 		return $this->data->name;
 	}
 	
	/**
 	 * Sets the app's home
 	 * 
 	 * @param string $home
 	 */
 	public function setHome($home)
 	{
 		$this->data->home = $home;
 	}
 	
 	/**
 	 * Returns the app's home
 	 * 
 	 * @return string
 	 */
 	public function getHome()
 	{
 		return $this->data->home;
 	}
 	
	/**
 	 * @param boolean If we are running from CLI
 	 */
 	public function setCLI($boolean)
 	{
 		$this->data->cli = (bool)$boolean;
 	}
 	
 	/**
 	 * @return boolean boolean If we are running from CLI
 	 */
 	public function getCLI()
 	{
 		return $this->data->cli;
 	}
}