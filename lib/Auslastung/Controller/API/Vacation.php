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
 * Vacation controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Vacation.php 61 2009-04-19 12:25:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Vacation controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Vacation.php 61 2009-04-19 12:25:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Vacation extends Auslastung_Controller_API_Base
{
	protected $model = 'Model_Vacation';

	protected function addEntry()
	{
		$Person = $this->getPerson();
		if (!$this->hasInputErrors()) {
			$Vacation = new Model_Vacation;
			$Vacation->person = $Person->id;
			$Vacation->type = $this->Request->getInput( 'type' );
			$Vacation->start = strftime('%Y-%m-%d', strtotime($this->Request->getInput('start')));
			$end = $this->Request->getInput('end');
			$Vacation->end = empty($end) ? null : strftime('%Y-%m-%d', strtotime($end));
			$days = $this->Request->getInput('days');
			$Vacation->days = empty($days) ? null : Auslastung_NumberHelper::getFloat($days);
			$Vacation->description = $this->Request->getInput('description');
			$Vacation->save();
			$this->Response->setResult($Vacation->toArray(), 1);
		}
	}

	protected function updateEntry()
	{
		$Person = $this->getPerson();
		$this->Entry->person = $Person->id;
		$this->Entry->type = $this->Request->getInput( 'type' );
		$this->Entry->start = strftime('%Y-%m-%d', strtotime($this->Request->getInput('start')));
		$end = $this->Request->getInput('end');
		$this->Entry->end = empty($end) ? null : strftime('%Y-%m-%d', strtotime($end));
		$days = $this->Request->getInput('days');
		$this->Entry->days = empty($days) ? null : Auslastung_NumberHelper::getFloat($days);
		$this->Entry->description = $this->Request->getInput('description');
		$this->Entry->save();
		$this->Response->setResult($this->Entry->toArray(), 1);
	}
}
