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
 * Unit controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Unit.php 75 2009-05-03 19:00:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Unit controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Unit.php 75 2009-05-03 19:00:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Unit extends Auslastung_Controller_API_Base
{
	protected $model = 'Model_Unit';

	protected function addEntry()
	{
		$Unit = new Model_Unit;
		$Unit->name = $this->Request->getInput( 'name' );
		$Unit->organization = Auslastung_Session::getInstance()->getOrganizationId();
		$Unit->save();
		$this->Response->setResult( $Unit->toArray(), 1 );
	}

	protected function updateEntry()
	{
		$this->Entry->organization = Auslastung_Session::getInstance()->getOrganizationId();
		$this->Entry->name = $this->Request->getInput( 'name' );
		$this->Entry->save();
		$this->Response->setResult( $this->Entry->toArray(), 1 );
	}
}
