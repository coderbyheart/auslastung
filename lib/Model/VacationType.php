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
 * Model for Vacation Types
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: VacationType.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Vacation Types
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: VacationType.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_VacationType extends Doctrine_Record
{
	public static $defaultVacationTypes = array(
		array(
			'name'        => 'Urlaub',
			'color'       => '79812A',
			'textcolor'	  => 'f0f0f0',
		),
		array(
			'name'        => 'Abwesend',
			'color'       => 'D5772A',
			'textcolor'	  => 'f0f0f0',
		),
	);
	
	public function setTableDefinition()
    {
        $this->setTableName('vacation_type');
        $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('organization', 'integer', 4, array('type' => 'integer', 'length' => 4, 'unsigned' => 1));
        $this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
        $this->hasColumn('color', 'string', 6, array('type' => 'string', 'length' => 6, 'fixed' => true));
        $this->hasColumn('textcolor', 'string', 6, array('type' => 'string', 'length' => 6, 'fixed' => 1));
    }

    public function setUp()
    {
        $this->hasOne('Organization', array('local' => 'organization', 'foreign' => 'id'));
        $this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
    }

    protected function validate()
    {
		if (!preg_match('/^[A-F0-9]+$/i', $this->color)) $this->getErrorStack()->add('color', 'format');
		if (!empty($this->textcolor) && !preg_match('/^[A-F0-9]+$/i', $this->textcolor)) $this->getErrorStack()->add('textcolor', 'format');
	}
}