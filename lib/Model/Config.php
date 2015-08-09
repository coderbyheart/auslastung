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
 * Model for Configs
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Config.php 75 2009-05-03 19:00:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Configs
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Config.php 75 2009-05-03 19:00:59Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Config extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('config');
        $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('organization', 'integer', 4, array('type' => 'integer', 'length' => 4, 'unsigned' => 1));
        $this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
        $this->hasColumn('value', 'string', 255, array('type' => 'string', 'length' => 255));
    }

    public function setUp()
    {
        $this->hasOne('Model_Organization as Organization', array('local' => 'organization', 'foreign' => 'id'));
    }
}