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
 * Base class, contains logger and error handler
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Base.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * Base class, contains logger and error handler
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Base.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
abstract class Auslastung_App_Base
{
	/**
	 * @var Auslastung_App_Config
	 */
	private $config;

	/**
	 * Constructor
	 *
	 * @param Auslastung_App_Options Options
	 */
	public function __construct(Auslastung_App_Options $options = null)
	{
		// Load EngineRoom
		require_once dirname(__FILE__) . '/../../EngineRoom/Autoloader.php';
		new EngineRoom_Autoloader();

		if ($options === null) $options = new Auslastung_App_Options();
		$this->config = $options->isTest() ? new Auslastung_App_TestConfig() : new Auslastung_App_Config();
		// Push some config vars to Env
		Auslastung_Environment::setSvnRev($this->config->getSvnRev());
		Auslastung_Environment::setTracUrl($this->config->getTracUrl());
		Auslastung_Environment::setName($this->config->getName());
        Auslastung_Environment::setBaseHref($this->config->getBaseHref());
        Auslastung_Environment::setIsTest($options->isTest());

		// Start session
		if (!headers_sent()) session_start();

		// Load ez Components
		require_once 'ezc/Base/base.php';
		spl_autoload_register(array('ezcBase', 'autoload'));
		
		// Load Doctrine
		require_once 'Doctrine.php'; 
		spl_autoload_register(array('Doctrine', 'autoload')); 
		$DoctrineManager = Doctrine_Manager::getInstance(); 
		$connection = $DoctrineManager->openConnection($this->config->getDsn()); 
		$connection->setCharset('UTF8'); 

		// Include FirePHP provided by PEAR
		if ($this->config->isDeveloper()) {
			require_once 'FirePHPCore/fb.php';
			$firephp = FirePHP::getInstance(true);
			$firephp->registerErrorHandler(true);
			$firephp->registerExceptionHandler();
			$firephp->registerAssertionHandler(true, false);
		}
	}

	/**
	 * @return Auslastung_App_Config
	 **/
	public function getConfig()
	{
		return $this->config;
	}

 	public function getVersion()
 	{
 		return $this->config->getSvnRev();
 	}
}