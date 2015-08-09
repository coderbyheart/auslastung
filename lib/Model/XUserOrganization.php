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
 * Crosstable class for assigning organizations to users
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: XUserOrganization.php 86 2009-06-19 09:55:32Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Crosstable class for assigning organizations to users
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: XUserOrganization.php 86 2009-06-19 09:55:32Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_XUserOrganization extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('x_user_organization');
        $this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('user', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('organization', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('z_ts_created', 'timestamp', null, array('type' => 'timestamp'));
        $this->hasColumn('z_ts_modified', 'timestamp', null, array('type' => 'timestamp'));
    }

    public function setUp()
    {
        $this->hasOne('Model_User as User', array('local' => 'user', 'foreign' => 'id'));
        $this->hasOne('Model_Organization as Organization', array('local' => 'organization', 'foreign' => 'id'));
    }

    public function postInsert($event)
    {
		$body = array();
		$body[] = 'Dir wurde eine Freigabe erteilt.';
		$body[] = '';
		$body[] = 'Du kannst nun an der Auslastung von';
		$body[] = '  ' . $this->Organization->name;
		$body[] = 'mitarbeiten';

		$Mailer = new Auslastung_Mailer();
		$Mailer->addTo($this->User);
		$Mailer->setSubject('Freigabe für ' . $this->Organization->name . ' erteilt');
		$Mailer->setBody(join("\n", $body));
		$Mailer->send();

		$OrgaFeed = new Model_OrganizationFeed;
		$OrgaFeed->organization = $this->Organization->id;
		$OrgaFeed->operator = Auslastung_Session::getInstance()->getUserId();
		$OrgaFeed->subject = $this->User->id;
		$OrgaFeed->action = Model_OrganizationFeed::ACTION_TEAM_ADD;
		$OrgaFeed->save();
    }

    public function preDelete($event)
    {
		$body = array();
		$body[] = 'Dir wurde eine Freigabe entzogen.';
		$body[] = '';
		$body[] = 'Du kannst nun leider nicht mehr an der Auslastung von';
		$body[] = '  ' . $this->Organization->name;
		$body[] = 'mitarbeiten';

		$Mailer = new Auslastung_Mailer();
		$Mailer->addTo($this->User);
		$Mailer->setSubject('Freigabe für ' . $this->Organization->name . ' entzogen');
		$Mailer->setBody(join("\n", $body));
		$Mailer->send();

		$OrgaFeed = new Model_OrganizationFeed;
		$OrgaFeed->organization = $this->Organization->id;
		$OrgaFeed->operator = Auslastung_Session::getInstance()->getUserId();
		$OrgaFeed->subject = $this->User->id;
		$OrgaFeed->action = Model_OrganizationFeed::ACTION_TEAM_REMOVE;
		$OrgaFeed->save();
    }
}