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
 * Assignment controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Assignment.php 185 2012-01-02 10:56:41Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Assignment controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Assignment.php 185 2012-01-02 10:56:41Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Assignment extends Auslastung_Controller_API_Base
{
	protected $model = 'Model_Assignment';

	protected function addEntry()
	{
		$Project = $this->getProject();
		$Person = $this->getPerson();

		if ( !$this->hasInputErrors() ) {
			$Assignment = new Model_Assignment;
			$Assignment->project = $Project->id;
			$Assignment->person = $Person->id;
			$Assignment->start = strftime( '%Y-%m-%d', strtotime( $this->Request->getInput( 'start' ) ) );
			$Assignment->manual_end = $this->Request->getInput( 'manual_end' );
			$Assignment->distribute_duration = $this->Request->getInput( 'distribute_duration' );
			$end = $this->Request->getInput( 'end' );
			$Assignment->end = empty( $end ) ? null : strftime( '%Y-%m-%d', strtotime( $end ) );
			$Assignment->probability = $this->Request->getInput( 'probability' );
			$Assignment->duration = (int)$this->Request->getInput( 'duration' );
			$Assignment->description = $this->Request->getInput( 'description' );
            $Assignment->is_homeoffice = (boolean)$this->Request->getInput('is_homeoffice');
			$Assignment->save();
			$this->Response->setResult( $Assignment->toArray(), 1 );
		}
	}

	protected function updateEntry()
	{
		$Project = $this->getProject();
		$Person = $this->getPerson();
		$this->Entry->project = $Project->id;
		$this->Entry->person = $Person->id;
		$this->Entry->start = strftime( '%Y-%m-%d', strtotime( $this->Request->getInput( 'start' ) ) );
		$this->Entry->manual_end = $this->Request->getInput( 'manual_end' );
		$this->Entry->distribute_duration = $this->Request->getInput( 'distribute_duration' );
		$end = $this->Request->getInput( 'end' );
		$this->Entry->end = empty( $end ) ? null : strftime( '%Y-%m-%d', strtotime( $end ) );
		$this->Entry->probability = $this->Request->getInput( 'probability' );
		$this->Entry->duration = (int)$this->Request->getInput( 'duration' );
		$this->Entry->description = $this->Request->getInput( 'description' );
        $this->Entry->is_homeoffice = (boolean)$this->Request->getInput('is_homeoffice');
		$this->Entry->save();
		$this->Response->setResult( $this->Entry->toArray(), 1 );
	}
}
