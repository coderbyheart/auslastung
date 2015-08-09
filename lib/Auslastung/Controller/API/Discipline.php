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
 * Discipline controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Discipline.php 28 2009-03-02 20:45:02Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
 
/**
 * Discipline controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Discipline.php 28 2009-03-02 20:45:02Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Discipline extends Auslastung_Controller_API_Base
{
	protected $model = 'Model_Discipline';

	protected function addEntry()
	{
		$Discipline = new Model_Discipline;
		$Discipline->name = $this->Request->getInput( 'name' );
		$Discipline->save();
		$this->Response->setResult( $Discipline->toArray(), 1 );
	}
	
	protected function updateEntry()
	{
		$this->Entry->name = $this->Request->getInput( 'name' );
		$this->Entry->save();
		$this->Response->setResult( $this->Entry->toArray(), 1 );
	}
}
