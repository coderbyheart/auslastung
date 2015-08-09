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
 * Model for User's Config
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: UserConfig.php 138 2011-01-02 17:21:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for User's Config
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: UserConfig.php 138 2011-01-02 17:21:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_UserConfig extends Doctrine_Record
{
	const CURRENT_ORGANIZATION = 'current_organization';
	const ORGANIZATION_FEED_DISMISSTIME = 'organization_feed_dismisstime';

    public function setTableDefinition()
    {
        $this->setTableName('user_config');
        $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('user', 'integer', 4, array('type' => 'integer', 'length' => 4, 'unsigned' => 1));
        $this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
        $this->hasColumn('value', 'string', 255, array('type' => 'string', 'length' => 255));
        $this->hasColumn('z_ts_created', 'timestamp', null, array('type' => 'timestamp'));
        $this->hasColumn('z_ts_modified', 'timestamp', null, array('type' => 'timestamp'));
    }

    public function setUp()
    {
        $this->hasOne('Model_User as User', array('local' => 'user', 'foreign' => 'id'));
    }

    public function preInsert($event)
    {
    	$this->z_ts_created = Auslastung_Environment::getTimestamp();
    }

    public function preUpdate($event)
    {
    	$this->z_ts_modified = Auslastung_Environment::getTimestamp();
    }

    /**
     * Returns public configuration directives
     *
     * @return array
     */
	public static function getPublicDirectives()
	{
		return array(
			self::CURRENT_ORGANIZATION,
			self::ORGANIZATION_FEED_DISMISSTIME,
		);
	}
}