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
 * Base controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Base.php 100 2009-07-20 11:19:41Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Base controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Base.php 100 2009-07-20 11:19:41Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
abstract class Auslastung_Controller_API_Base implements Auslastung_Controller_Base
{
	protected $Response;
	protected $Request;
	protected $Entry;
	protected $model;
	protected $inputErrors;

	public function __construct( Auslastung_Request $Request )
	{
		$this->Response = new Auslastung_Response_JSON();
		$this->Request = $Request;
		switch($Request->getMethod()) {
		case 'POST':
			if ( $Request->getParam( 'id' ) === null ) {
				$this->addEntry();
			} else {
				// Find entry
				$this->Entry = $this->getEntryById( $Request->getParam( 'id' ) );
				if (!($this->Entry instanceof Model_Abstract)) throw new Auslastung_Exception('Entry is not a Model_Abstract but a ' . get_class($this->Entry) . '.');
				if ( $Request->getInput( 'method' ) === 'delete'
				|| $Request->getInput( 'method', null, null, 'GET' ) === 'delete' ) {
					if (call_user_func(array(get_class($this->Entry), 'isDeleteAble')) === true) {
						$this->Entry->delete();
					} else {
						throw new Auslastung_Exception('Entry is not deleteable.');
					}
				} else {
					$this->updateEntry();
				}
			}
			break;
		}
	}
	
	/**
	 * @param int ID
	 * @return Model_Abstract
	 */
	protected function getEntryById($id)
	{
		$Entry = Doctrine::getTable( $this->model )->find( $this->Request->getParam( 'id' ) );
		if ( $Entry === false ) throw new Auslastung_Exception( 'Entry not found: ' . $this->Request->getParam( 'id' ) );
		return $Entry;
	}

	abstract protected function addEntry();

	/**
	* Get response of controller
	* @return Auslastung_Response
	*/
	public function getResponse()
	{
		return $this->Response;
	}

	/**
	 * Checks whether input errors occurred
	 *
	 * @return bool
	 */
	public function hasInputErrors()
	{
		return $this->inputErrors !== null;
	}

	/**
	 * Adds an input error
	 *
	 * @param string field name
	 */
	public function inputError( $field )
	{
		if ( $this->inputErrors === null ) $this->inputErrors = array();
		$this->inputErrors[] = $field;
	}

	/**
	 * Returns input errors
	 *
	 * @return array|null
	 */
	 public function getInputErrors()
	 {
		 return $this->inputErrors;
	 }

	/**
	 * @return Model_Person
	 */
	protected function getPerson()
	{
		// Find Person
		$Person = Doctrine_Query::create()
			->from( 'Model_Person' )
			->where( 'organization = ? AND name = ?', array( Auslastung_Session::getInstance()->getOrganizationId(), $this->Request->getInput( 'person__name' ) ) )
			->fetchOne();
		if ( !$Person ) {
			$this->inputError( 'person__name' );
		}
		return $Person;
	}

	/**
	 * @return Model_Project
	 */
	protected function getProject()
	{
		// Find Project
		$Project = Doctrine_Query::create()
			->from( 'Model_Project' )
			->where( 'organization = ? AND name = ?', array( Auslastung_Session::getInstance()->getOrganizationId(), $this->Request->getInput( 'project__name' ) ) )
			->fetchOne();
		if ( !$Project ) {
			$Project = new Model_Project;
			$Project->organization = Auslastung_Session::getInstance()->getOrganizationId();
			$Project->name = $this->Request->getInput( 'project__name' );
			$Project->save();
		}
		return $Project;
	}
}