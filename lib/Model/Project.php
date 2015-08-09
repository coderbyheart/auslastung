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
 * Model for Projects
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Project.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
 
/**
 * Model for Projects
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Project.php 167 2011-12-28 18:04:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Project extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('project');
		$this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('organization', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
		$this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
	}
	
	public function setUp()
	{	
		$this->hasOne('Model_Organization as Organization', array('local' => 'organization', 'foreign' => 'id'));
		$this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
	}
}