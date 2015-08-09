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
 * Model for Assignments
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Assignment.php 185 2012-01-02 10:56:41Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Assignments
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Assignment.php 185 2012-01-02 10:56:41Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Assignment extends Model_Abstract
{
    private $hoursOnDay;

    public function setTableDefinition()
    {
        $this->setTableName('assignment');
        $this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('project', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('person', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('start', 'date', null, array('type' => 'date'));
        $this->hasColumn('end', 'date', null, array('type' => 'date'));
        $this->hasColumn('manual_end', 'date', null, array('type' => 'date'));
        $this->hasColumn('distribute_duration', 'enum', 1, array('type' => 'enum', 'length' => 1, 'values' => array(0 => '0', 1 => '1'), 'default' => '0', 'notnull' => true));
        $this->hasColumn('duration', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('probability', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('description', 'string', null, array('type' => 'string'));
        $this->hasColumn('is_homeoffice', 'enum', 1, array('type' => 'enum', 'length' => 1, 'values' => array(0 => '0', 1 => '1'), 'default' => '0', 'notnull' => true));
        $this->hasColumn('author', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
    }

    public function setUp()
    {
        $this->hasOne('Model_Project as Project', array('local' => 'project', 'foreign' => 'id'));
        $this->hasOne('Model_Person as Person', array('local' => 'person', 'foreign' => 'id'));
        $this->hasOne('Model_User as Author', array('local' => 'author', 'foreign' => 'id'));
        $this->hasOne('Model_Probability as Probability', array('local' => 'probability', 'foreign' => 'id'));
        $this->hasMany('Model_AssignmentDay as Days', array('local' => 'id', 'foreign' => 'assignment'));
        $this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
    }

    public function postSave($event)
    {
        Doctrine_Query::create()
            ->from('Model_AssignmentDay')
            ->where('assignment = ?', array($this->id))
            ->delete()
            ->execute();
        foreach ($this->hoursOnDay as $date => $hours) {
            $AssignmentDay = new Model_AssignmentDay;
            $AssignmentDay->date = $date;
            $AssignmentDay->hours = $hours;
            $AssignmentDay->assignment = $this->id;
            $AssignmentDay->save();
        }
    }

    public function preSave($event)
    {
        $this->author = Auslastung_Session::getInstance()->getUserId();
        if (empty($this->manual_end) || $this->manual_end === '0000-00-00') $this->manual_end = null;

        $StartDate = new DateTime();
        $StartDate->setTime(0, 0, 0);
        $StartDate->setDate(substr($this->start, 0, 4), substr($this->start, 5, 2), substr($this->start, 8, 2));

        $Person = $this->getPerson();
        if (!empty($this->manual_end) && (int)$this->distribute_duration === 1) {
            $EndDate = new DateTime();
            $EndDate->setTime(23, 59, 59);
            $EndDate->setDate(substr($this->manual_end, 0, 4), substr($this->manual_end, 5, 2), substr($this->manual_end, 8, 2));
            $this->end = strftime('%Y-%m-%d', $EndDate->format('U'));
            $this->duration = (int)$this->duration;
            $Person->getHours($StartDate, $EndDate, $this->duration);
        } else if (!empty($this->manual_end)) {
            $EndDate = new DateTime();
            $EndDate->setTime(23, 59, 59);
            $EndDate->setDate(substr($this->manual_end, 0, 4), substr($this->manual_end, 5, 2), substr($this->manual_end, 8, 2));
            $this->end = strftime('%Y-%m-%d', $EndDate->format('U'));
            $this->duration = $Person->getHoursBetweenDates($StartDate, $EndDate);
            $this->manual_end = null;
        } else {
            $EndDate = $Person->getEndDate($StartDate, $this->duration);
        }
        $this->hoursOnDay = $Person->getHoursOnDay();
        $this->end = strftime('%Y-%m-%d', $EndDate->format('U'));
    }

    public static function getForm($id = null)
    {
        if ($id !== null) {
            $Entry = Doctrine_Query::create()
                ->from(__CLASS__ . ' a')
                ->leftJoin('a.Project')
                ->leftJoin('a.Person')
                ->where('id = ?', array($id))
                ->fetchOne();
        }
        $session = Auslastung_Session::getInstance();
        $defaultPropability = $session->getOrganizationConfig('default_probability');
        $probs = array();
        foreach (Doctrine_Query::create()
                     ->from('Model_Probability')
                     ->where('organization = ?', array($session->getOrganizationId()))
                     ->execute() as $Probability) $probs[$Probability->id] = $Probability->name . ($Probability->percentage == null ? '' : ' (' . $Probability->percentage . '%)');
        $return = array(
            new Auslastung_FormField(array(
                'name' => 'project__name',
                'type' => 'text',
                'label' => 'Projekt',
                'autocomplete' => true,
                'value' => $id === null ? null : $Entry->Project->name,
            )),
            new Auslastung_FormField(array(
                'name' => 'person__name',
                'type' => 'text',
                'label' => 'Person',
                'autocomplete' => true,
                'value' => $id === null ? null : $Entry->Person->name,
            )),
            new Auslastung_FormField(array(
                'name' => 'start',
                'type' => 'date',
                'label' => 'Startdatum',
                'value' => $id === null ? null : $Entry->start,
            )),
            new Auslastung_FormField(array(
                'name' => 'duration',
                'type' => 'text',
                'label' => 'Dauer in Stunden',
                'value' => $id === null ? null : $Entry->duration,
            )),
            new Auslastung_FormField(array(
                'name' => 'manual_end',
                'type' => 'date',
                'label' => 'Enddatum',
                'value' => $id === null ? null : $Entry->manual_end,
            )),
            new Auslastung_FormField(array(
                'name' => 'distribute_duration',
                'type' => 'boolean',
                'label' => 'Dauer verteilen?',
                'value' => $id === null ? null : $Entry->distribute_duration,
            )),
            new Auslastung_FormField(array(
                'name' => 'probability',
                'type' => 'select',
                'label' => 'Wahrscheinlichkeit',
                'values' => $probs,
                'value' => $id === null ? $defaultPropability !== null ? $defaultPropability : null : $Entry->probability,
            )),
            new Auslastung_FormField(array(
                'name' => 'description',
                'type' => 'text',
                'label' => 'Beschreibung',
                'value' => $id === null ? null : $Entry->description,
            )),
            new Auslastung_FormField(array(
                'name' => 'is_homeoffice',
                'type' => 'boolean',
                'label' => 'HomeOffice?',
                'value' => $id === null ? null : $Entry->is_homeoffice,
            )),
        );
        if ($id !== null) {
            $return[] = new Auslastung_FormField(array(
                'name' => 'end',
                'type' => 'static',
                'label' => 'Enddatum',
                'value' => $id === null ? null : $Entry->end,
            ));
        }
        return $return;
    }

    protected function validate()
    {
        if (empty($this->probability)) $this->getErrorStack()->add('probability', 'missing');
        if (Auslastung_DateHelper::isHoliday(new DateTime($this->start))) $this->getErrorStack()->add('start', 'holiday');
        if (Auslastung_DateHelper::isWeekend(new DateTime($this->start))) $this->getErrorStack()->add('start', 'weekend');
        if (empty($this->manual_end) || $this->manual_end === '0000-00-00') $this->manual_end = null;
        if (empty($this->duration) && empty($this->manual_end)) {
            $this->getErrorStack()->add('duration', 'missing');
            $this->getErrorStack()->add('manual_end', 'missing');
        }
        if (!empty($this->duration) && !empty($this->manual_end) && (int)$this->distribute_duration === 0) {
            $this->getErrorStack()->add('distribute_duration', 'set');
            $this->getErrorStack()->add('duration', 'set');
            $this->getErrorStack()->add('manual_end', 'set');
        }
    }

    public function getHoursOnDay(DateTime $Date)
    {
        if ($Date->format('Y-m-d') < $this->start || $Date->format('Y-m-d') > $this->end) return (float)0;
        foreach ($this->Days as $AssignmentDay) {
            if ($AssignmentDay->date === $Date->format('Y-m-d')) return (float)$AssignmentDay->hours;
        }
        return (float)0;
    }

    private function getPerson()
    {
        return Doctrine_Query::create()
            ->from('Model_Person p')
            ->where('p.id = ?', array($this->person))
            ->fetchOne();
    }
}
