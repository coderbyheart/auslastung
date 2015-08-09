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
 * CLI app
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: CLI.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage App
 */

/**
 * CLI app
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: CLI.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage App
 */
class Auslastung_App_CLI extends Auslastung_App_Base
{
	/**
	 * Constructor
	 *
	 * @param Auslastung_App_Options Options
	 */
	public function __construct(Auslastung_App_Options $options = null)
	{
		parent::__construct($options);
		new EngineRoom_CLI_App(
			EngineRoom_CLI_AppOptions::create()
				->name('Auslastung CLI')
				->modules(array(
					new Auslastung_CLI_Install($this),
					new Auslastung_CLI_Update($this)
				))
		);
	}
}