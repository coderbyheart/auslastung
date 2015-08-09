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
 * Base class for CLI classes
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Base.php 141 2011-01-02 18:29:51Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage CLI
 */

/**
 * Base class for CLI classes
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Base.php 141 2011-01-02 18:29:51Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage CLI
 */
abstract class Auslastung_CLI_Base implements EngineRoom_CLI_IAppModule
{
	protected $app;
	protected $cliApp;

	public function __construct(Auslastung_App_Base $app)
	{
		$this->app = $app;
	}

	/**
	 * @see EngineRoom_CLI_IAppModule::setCliApp()
	 */
	public function setCliApp(EngineRoom_CLI_App $cliApp)
	{
		$this->cliApp = $cliApp;
	}
}