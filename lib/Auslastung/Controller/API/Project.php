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
 * Project controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Project.php 6 2009-01-02 23:12:30Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
 
/**
 * Project controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Project.php 6 2009-01-02 23:12:30Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Project extends Auslastung_Controller_API_Base
{
	protected $model = 'Model_Project';

	protected function addEntry()
	{
		$Project = new Model_Project;
		$Project->name = $this->Request->getInput( 'name' );
		$Project->save();
		$this->Response->setResult( $Project->toArray(), 1 );
	}
}
