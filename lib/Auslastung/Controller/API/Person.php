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
 * Person controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Person.php 75 2009-05-03 19:00:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Person controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Person.php 75 2009-05-03 19:00:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Person extends Auslastung_Controller_API_Base
{
	protected $model = 'Model_Person';

	protected function addEntry()
	{
		$Unit = $this->getUnit();
		$Discipline = $this->getDiscipline();
		$Person = new Model_Person;
		$Person->organization = Auslastung_Session::getInstance()->getOrganizationId();
		$Person->name = $this->Request->getInput( 'name' );
		$Person->unit = $Unit->id;
		$Person->discipline = $Discipline->id;
		$Person->save();
		$this->Response->setResult( $Person->toArray(), 1 );
	}

	protected function updateEntry()
	{
		$Unit = $this->getUnit();
		$Discipline = $this->getDiscipline();
		$this->Entry->organization = Auslastung_Session::getInstance()->getOrganizationId();
		$this->Entry->name = $this->Request->getInput( 'name' );
		$this->Entry->unit = $Unit->id;
		$this->Entry->discipline = $Discipline->id;
		$this->Entry->save();
		$this->Response->setResult( $this->Entry->toArray(), 1 );
	}

	protected function getUnit()
	{
		// Find unit
		$Unit = Doctrine_Query::create()
			->from( 'Model_Unit' )
			->where( 'organization = ? AND name = ?', array( Auslastung_Session::getInstance()->getOrganizationId(), $this->Request->getInput( 'unit__name' ) ) )
			->fetchOne();
		if ( !$Unit ) {
			$Unit = new Model_Unit;
			$Unit->organization = Auslastung_Session::getInstance()->getOrganizationId();
			$Unit->name = $this->Request->getInput( 'unit__name' );
			$Unit->save();
		}
		return $Unit;
	}

	protected function getDiscipline()
	{
		// Find discipline
		$Discipline = Doctrine_Query::create()
			->from( 'Model_Discipline' )
			->where( 'name = ?', array( $this->Request->getInput( 'discipline__name' ) ) )
			->fetchOne();
		if ( !$Discipline ) {
			$Discipline = new Model_Discipline;
			$Discipline->name = $this->Request->getInput( 'discipline__name' );
			$Discipline->save();
		}
		return $Discipline;
	}
}
