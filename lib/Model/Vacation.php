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
 * Model for Vacations
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Vacation.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Vacations
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Vacation.php 175 2011-12-31 16:28:39Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Vacation extends Model_Abstract
{
	public function setTableDefinition()
	{
        $this->setTableName('vacation');
        $this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('person', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('type', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('start', 'date', null, array('type' => 'date'));
        $this->hasColumn('end', 'date', null, array('type' => 'date'));
        $this->hasColumn('days', 'float', 13, array('type' => 'float', 'length' => 13));
        $this->hasColumn('duration', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
        $this->hasColumn('description', 'string', null, array('type' => 'string'));
        $this->hasColumn('author', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
	}

	public function setUp()
	{
		$this->hasOne('Model_Person as Person', array('local' => 'person', 'foreign' => 'id'));
		$this->hasOne('Model_User as Author', array('local' => 'author', 'foreign' => 'id'));
		$this->hasOne('Model_VacationType as Type', array('local' => 'type', 'foreign' => 'id'));
		$this->hasMany('Model_VacationDay as Days', array('local' => 'id', 'foreign' => 'vacation'));
		$this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
	}

	public static function getForm($id = null)
	{
		if ($id !== null) {
			$Entry = Doctrine_Query::create()
				->from(__CLASS__ . ' a')
				->leftJoin('a.Person')
				->where('id = ?', array($id))
				->fetchOne();
		}
		$types = array();
		foreach( Doctrine_Query::create()
				->from( 'Model_VacationType' )
				->where( 'organization = ?', array( Auslastung_Session::getInstance()->getOrganizationId() ) )
				->execute() as $Type ) $types[ $Type->id ] = $Type->name;
		$return = array(
			new Auslastung_FormField(array(
				'name' => 'person__name',
				'type' => 'text',
				'label' => 'Person',
				'autocomplete' => true,
				'value' => $id === null ? null : $Entry->Person->name,
			)),
			new Auslastung_FormField(array(
				'name' => 'type',
				'type' => 'select',
				'label' => 'Art',
				'values' => $types,
				'value' => $id === null ? null : $Entry->type,
			)),
			new Auslastung_FormField(array(
				'name' => 'start',
				'type' => 'date',
				'label' => 'Startdatum',
				'value' => $id === null ? null : $Entry->start,
			)),
			new Auslastung_FormField(array(
				'name' => 'end',
				'type' => 'date',
				'label' => 'Enddatum',
				'value' => $id === null ? null : $Entry->end,
			)),
			new Auslastung_FormField(array(
				'name' => 'days',
				'type' => 'text',
				'label' => 'Tage',
				'value' => $id === null ? null : $Entry->days,
			)),
			new Auslastung_FormField(array(
				'name' => 'description',
				'type' => 'text',
				'label' => 'Beschreibung',
				'value' => $id === null ? null : $Entry->description,
			)),
		);
		return $return;
	}

	protected function validate()
    {
		if (Auslastung_DateHelper::isWeekend(new DateTime($this->start))) $this->getErrorStack()->add('start', 'weekend');
        if (Auslastung_DateHelper::isHoliday(new DateTime($this->start))) $this->getErrorStack()->add('start', 'holiday');
		if (empty($this->days) && empty($this->duration)) {
			$this->getErrorStack()->add('days', 'missing');
			$this->getErrorStack()->add('duration', 'missing');
		} else if (empty($this->end)) {
			$this->getErrorStack()->add('end', 'missing');
		}
		if (empty($this->type)) $this->getErrorStack()->add('type', 'missing');
	}

	public function preSave($event)
	{
		$this->author = Auslastung_Session::getInstance()->getUserId();
		if(empty($this->duration)) $this->duration = $this->days * 8;
		if(empty($this->days)) $this->days = $this->duration / 8;
		$StartDate = new DateTime();
		$StartDate->setTime(0, 0, 0);
		$StartDate->setDate(substr($this->start, 0, 4), substr($this->start, 5, 2), substr($this->start, 8, 2));
		if (empty($this->end)) {
			$this->duration = $this->days * 8;
			$EndDate = Auslastung_DateHelper::getEndDate($StartDate, $this->duration);
			$this->end = strftime('%Y-%m-%d', $EndDate->format('U'));
		} else {
			$EndDate = new DateTime();
			$EndDate->setTime(0, 0, 0);
			$EndDate->setDate(substr($this->end, 0, 4), substr($this->end, 5, 2), substr($this->end, 8, 2));
			$this->duration = Auslastung_DateHelper::getHoursBetweenDates($StartDate, $EndDate, null);
			$this->days = $this->duration / 8;
		}
	}

	public function postSave($event)
	{
		Doctrine_Query::create()
			->from('Model_VacationDay')
			->where('vacation = ?', array($this->id))
			->delete()
			->execute();
		$Date = new DateTime($this->start);
		for($i = $this->duration; $i > 0; $i -= 8) {
			if(Auslastung_DateHelper::isHoliday($Date)
			|| Auslastung_DateHelper::isWeekend($Date)) {
				$i += 8;
				$Date->modify('+1 day');
				continue;
			}
			$VacationDay = new Model_VacationDay;
			$VacationDay->date = $Date->format('Y-m-d');
			$VacationDay->hours = min($i, 8);
			$VacationDay->vacation = $this->id;
			$VacationDay->save();
			$Date->modify('+1 day');
		}
		$this->updateAssignments();
	}

	public function postDelete($event)
	{
		$this->updateAssignments();
	}

	private function updateAssignments()
	{
		// Find assignments this might affect
		foreach(Doctrine_Query::create()
			->from('Model_Assignment a')
			->where('a.person = :person', array(':person' => $this->person))
			->execute() as $Assignment) {
			$Assignment->save();
		}
	}

	public function getHoursOnDay(DateTime $Date)
	{
		if ($Date->format('Y-m-d') < $this->start || $Date->format('Y-m-d') > $this->end) return (float)0;
		foreach($this->Days as $VacationDay) {
			if($VacationDay->date === $Date->format('Y-m-d')) return (float)$VacationDay->hours;
		}
		return (float)0;
	}
}