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
 * Model for Vacation Days
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: VacationDay.php 59 2009-04-14 22:19:48Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Vacation Days
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: VacationDay.php 59 2009-04-14 22:19:48Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_VacationDay extends Doctrine_Record
{
	public function setTableDefinition()
	{
        $this->setTableName('vacation_day');
        $this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('vacation', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('date', 'date', null, array('type' => 'date'));
        $this->hasColumn('hours', 'float', 13, array('type' => 'float', 'length' => 13, 'unsigned' => 1));
    }

    public function setUp()
    {
        $this->hasOne('Model_Vacation as Vacation', array('local' => 'vacation',
                                        'foreign' => 'id'));
    }
}