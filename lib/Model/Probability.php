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
 * Model for Probabilitys
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Probability.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
 
/**
 * Model for Probabilitys
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Probability.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Probability extends Doctrine_Record
{
	public static $defaultPropabilities = array(
		array(
			'name'        => 'sicher',
			'percentage'  => 100,
			'color'       => '5DAD11',
			'textcolor'	  => '403B33',
		),
		array(
			'name'        => 'wahrscheinlich',
			'percentage'  => 75,
			'color'       => 'FFE115',
			'textcolor'	  => '403B33',
		),
		array(
			'name'        => 'unsicher',
			'percentage'  => 50,
			'color'       => 'FF9F15',
			'textcolor'	  => '403B33',
		),
	);
	
	public function setTableDefinition()
	{
		$this->setTableName('probability');
		$this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('organization', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
		$this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
		$this->hasColumn('percentage', 'integer', 3, array('type' => 'integer', 'length' => 3, 'unsigned' => 1));
		$this->hasColumn('color', 'string', 6, array('type' => 'string', 'length' => 6, 'fixed' => 1));
		$this->hasColumn('textcolor', 'string', 6, array('type' => 'string', 'length' => 6, 'fixed' => 1));
	}
	
	public function setUp()
	{	
		$this->hasOne('Model_Organization as Organization', array('local' => 'organization', 'foreign' => 'id'));
		$this->hasMany('Model_Assignment as Assignment', array('local' => 'id', 'foreign' => 'propability'));
		$this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
	}
	
	protected function validate()
    {
		if ((int)$this->percentage < 0 || (int)$this->percentage > 100) $this->getErrorStack()->add('percentage', 'length');
		if (!preg_match('/^[A-F0-9]+$/i', $this->color)) $this->getErrorStack()->add('color', 'format');
		if (!empty($this->textcolor) && !preg_match('/^[A-F0-9]+$/i', $this->textcolor)) $this->getErrorStack()->add('textcolor', 'format');
	}
}