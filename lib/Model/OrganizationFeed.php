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
 * Model for OrganizationFeed
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: OrganizationFeed.php 138 2011-01-02 17:21:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for OrganizationFeed
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: OrganizationFeed.php 138 2011-01-02 17:21:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_OrganizationFeed extends Doctrine_Record
{
	const ACTION_TEAM_ADD = 'organization.team.add';
	const ACTION_TEAM_REMOVE = 'organization.team.remove';

    public function setTableDefinition()
    {
        $this->setTableName('organization_feed');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 10,
             'unsigned' => 1,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('organization', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10,
             'unsigned' => 1,
             ));
        $this->hasColumn('operator', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10,
             'unsigned' => 1,
             ));
        $this->hasColumn('subject', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10,
             'unsigned' => 1,
             ));
        $this->hasColumn('action', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('z_ts_created', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
    }

    public function setUp()
    {
        $this->hasOne('Model_Organization as Organization', array('local' => 'organization', 'foreign' => 'id'));
        $this->hasOne('Model_User as Operator', array('local' => 'operator', 'foreign' => 'id'));
        $this->hasOne('Model_User as Subject', array('local' => 'subject', 'foreign' => 'id'));
    }

    public function preInsert($event)
	{
		$this->z_ts_created = Auslastung_Environment::getTimestamp();
	}
}