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
 * Model for Disciplines
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Discipline.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
 
/**
 * Model for Disciplines
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Discipline.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Discipline extends Model_Abstract
{
	public function setTableDefinition()
	{
		$this->setTableName('discipline');
        $this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('organization', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
	}
	
	public function setUp()
	{	
		$this->hasOne('Model_Organization as Organization', array('local' => 'organization', 'foreign' => 'id'));
		$this->hasMany('Model_Person as Person', array('local' => 'id', 'foreign' => 'discipline'));
		$this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
	}

	public function preSave( $event )
	{
		$this->organization = Auslastung_Session::getInstance()->getOrganizationId();
	}
	
	public static function getForm($id = null)
	{
		if ($id !== null) {
			$Entry = Doctrine_Query::create()
				->from(__CLASS__)
				->where('id = ?', array($id))
				->fetchOne();
		}
		$return = array(
			new Auslastung_FormField(array(
				'name' => 'organization',
				'type' => 'static',
				'label' => 'Firma',
				'value' => Auslastung_Session::getInstance()->getOrganizationName(),
			)),
			new Auslastung_FormField(array(
				'name' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'value' => $id === null ? null : $Entry->name,
			)),
		);

		return $return;
	}
}