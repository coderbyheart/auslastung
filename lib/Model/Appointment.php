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
 * Model for Appointments
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Holiday.php 19 2009-03-01 20:30:00Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Appointments
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Holiday.php 19 2009-03-01 20:30:00Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Appointment extends Model_Abstract
{
    const COLOR_DEFAULT_CONFIG = 'appointment.color.default';
    const COLOR_DEFAULT = '6B0EDC';
    const TEXTCOLOR_DEFAULT_CONFIG = 'appointment.textcolor.default';
    const TEXTCOLOR_DEFAULT = 'f0f0f0';
    const COLOR_HOLIDAY_CONFIG = 'appointment.color.holiday';
    const COLOR_HOLIDAY = 'cccccc';
    const TEXTCOLOR_HOLIDAY_CONFIG = 'appointment.textcolor.holiday';
    const TEXTCOLOR_HOLIDAY = 'a00000';

    public function setTableDefinition()
    {
        $this->setTableName('appointment');
        $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('organization', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('start', 'datetime', null, array('type' => 'datetime'));
        $this->hasColumn('end', 'datetime', null, array('type' => 'datetime'));
        $this->hasColumn('duration', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('description', 'string', null, array('type' => 'string'));
        $this->hasColumn('is_holiday', 'enum', 1, array('type' => 'enum', 'length' => 1, 'values' => array(0 => '0', 1 => '1'), 'default' => '0', 'notnull' => true));
        $this->hasColumn('author', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
    }

    public function setUp()
    {
        $this->hasOne('Model_Organization as Organization', array('local' => 'organization', 'foreign' => 'id'));
        $this->hasOne('Model_User as Author', array('local' => 'author', 'foreign' => 'id'));
        $this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
    }

    public function postDelete($event)
    {
        $this->updateAssignments();
    }

    private function updateAssignments()
    {
        // Find assignments this might affect
        foreach (Doctrine_Query::create()
                     ->from('Model_Assignment a')
                     ->leftJoin('a.Person p')
                     ->addWhere('p.organization = :organization AND a.start <= :start AND a.end >= :end', array(':organization' => $this->organization, ':start' => substr($this->start, 0, 10), ':end' => substr($this->end, 0, 10)))
                     ->execute() as $Assignment) {
            $Assignment->save();
        }
        // Find vacations this might affect
        foreach (Doctrine_Query::create()
                     ->from('Model_Vacation v')
                     ->leftJoin('v.Person p')
                     ->addWhere('p.organization = :organization AND v.start <= :start AND v.end >= :end', array(':organization' => $this->organization, ':start' => substr($this->start, 0, 10), ':end' => substr($this->end, 0, 10)))
                     ->execute() as $Vacation) {
            $Vacation->save();
        }
    }

    public static function getForm($id = null)
    {
        if ($id !== null) {
            $Entry = Doctrine_Query::create()
                ->from(__CLASS__ . ' a')
                ->where('id = ?', array($id))
                ->fetchOne();
        }
        $return = array(
            new Auslastung_FormField(array(
                'name' => 'description',
                'type' => 'text',
                'label' => 'Beschreibung',
                'mandatory' => true,
                'value' => $id === null ? null : $Entry->description,
            )),
            new Auslastung_FormField(array(
                'name' => 'day',
                'type' => 'date',
                'label' => 'Datum',
                'mandatory' => true,
                'value' => $id === null ? null : substr($Entry->start, 0, 10),
            )),
            new Auslastung_FormField(array(
                'name' => 'start_time',
                'type' => 'time',
                'label' => 'Beginn',
                'value' => $id === null ? null : substr($Entry->start, 11, 5),
            )),
            new Auslastung_FormField(array(
                'name' => 'end_time',
                'type' => 'time',
                'label' => 'Ende',
                'value' => $id === null ? null : substr($Entry->end, 11, 5),
            )),
            new Auslastung_FormField(array(
                'name' => 'is_holiday',
                'type' => 'boolean',
                'label' => 'Feiertag / Frei?',
                'value' => $id === null ? '0' : $Entry->is_holiday ? '1' : '0',
            )),
        );
        return $return;
    }

    public function preSave($event)
    {
        $this->author = Auslastung_Session::getInstance()->getUserId();

        $StartDate = new DateTime($this->start);
        $EndDate = new DateTime($this->end);
        // Die Dauer eines Feiertages wird rechnerisch auf 8 Stunden pro Tag begrenzt
        $d = ((((int)$EndDate->format('U') - (int)$StartDate->format('U')) / 3600) * (8 / 24)) / 8;
        $days = (int)$d;
        $this->duration = (int)($days * 8 + ($d - $days) * 24);
    }

    protected function validate()
    {
        if (empty($this->start)) {
            $this->getErrorStack()->add('start', 'missing');
        }
        if (empty($this->end)) {
            $this->getErrorStack()->add('end', 'missing');
        }
        if (empty($this->description)) {
            $this->getErrorStack()->add('description', 'missing');
        }
        // Termine über mehere Tage sind (noch?) nicht unterstützt
        if (!empty($this->start) && !empty($this->end)) {
            $StartDate = new DateTime($this->start);
            $EndDate = new DateTime($this->end);
            $MaxEnd = clone $StartDate;
            $MaxEnd->setTime(0, 0, 0);
            $MaxEnd->modify('+1 day');
            $d = (int)$EndDate->format('U') - (int)$StartDate->format('U');
            if ($d > 86400 || $EndDate > $MaxEnd) {
                $this->getErrorStack()->add('end', 'max_1_day');
            }
        }
    }

}