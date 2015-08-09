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
 * Model for Organizations
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Organization.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Organizations
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Organization.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Organization extends Model_Abstract
{
	public function setTableDefinition()
	{
		$this->setTableName('organization');
		$this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
        $this->hasColumn('z_created_by', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('z_ts_created', 'timestamp', null, array('type' => 'timestamp'));
        $this->hasColumn('z_ts_modified', 'timestamp', null, array('type' => 'timestamp'));
	}

	public function setUp()
	{
		$this->hasMany('Model_Unit as Unit', array('local' => 'id', 'foreign' => 'organization'));
		$this->hasMany('Model_Config as Config', array('local' => 'id', 'foreign' => 'organization'));
		$this->hasMany('Model_User as Users', array('local' => 'organization', 'foreign' => 'user', 'refClass' => 'Model_XUserOrganization'));
		$this->hasOne('Model_User as Owner', array('local' => 'z_created_by', 'foreign' => 'id'));
	}

 	public function preInsert($event)
    {
    	$this->z_ts_created = Auslastung_Environment::getTimestamp();
    }

    public function postInsert($event)
    {
		foreach( Model_Probability::$defaultPropabilities as $prop ) {
			$p = new Model_Probability;
			$p->name         = $prop[ 'name' ];
			$p->percentage   = $prop[ 'percentage' ];
			$p->color        = $prop[ 'color' ];
			$p->textcolor    = $prop[ 'textcolor' ];
			$p->organization = $this->id;
			$p->save();
		}

		foreach( Model_VacationType::$defaultVacationTypes as $type ) {
			$t = new Model_VacationType;
			$t->name         = $type[ 'name' ];
			$t->color        = $type[ 'color' ];
			$t->textcolor    = $type[ 'textcolor' ];
			$t->organization = $this->id;
			$t->save();
		}
    }

    public function preUpdate($event)
    {
    	$this->z_ts_modified = Auslastung_Environment::getTimestamp();
    }
    
    public static function getForm($id = null)
	{
		$return = array(
			new Auslastung_FormField(array(
				'name' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'value' => $id === null ? null : Auslastung_Session::getInstance()->getOrganizationName(),
			)),
		);

		return $return;
	}

	/**
	 * Return wheter this entry may be deleted by the user
	 * 
	 * @return bool
	 */
	public static function isDeleteAble()
	{
		return false;
	}
}