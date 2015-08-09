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
 * App for Unit Tests
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Test.php 139 2011-01-02 18:29:47Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage App
 */

/**
 * App for Unit Tests
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Test.php 139 2011-01-02 18:29:47Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage App
 */
class Auslastung_App_Test extends Auslastung_App_Base
{
	/**
	 * @var Auslastung_Session
	 */
	private $session;
	
	/**
	 * Constructor
	 *
	 * @param Auslastung_App_Options Options
	 */
	public function __construct(Auslastung_App_Options $options = null)
	{
		parent::__construct(Auslastung_App_Options::create()->isTest(true));
		$this->session = Auslastung_Session::create($this->getVersion());
	}
}