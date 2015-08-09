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
 * Model for Persons
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Person.php 173 2011-12-30 19:26:09Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */

/**
 * Model for Persons
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Person.php 173 2011-12-30 19:26:09Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Person extends Model_Abstract
{
	private $hoursOnDay;
	private $hoursPerDay = 8;

	public function setTableDefinition()
	{
		$this->setTableName('person');
		$this->hasColumn('id', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('organization', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
		$this->hasColumn('unit', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
		$this->hasColumn('discipline', 'integer', 10, array('type' => 'integer', 'length' => 10, 'unsigned' => 1));
		$this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => 255));
	}

	public function setUp()
	{
		$this->hasOne('Model_Organization as Organization', array('local' => 'organization', 'foreign' => 'id'));
		$this->hasOne('Model_Unit as Unit', array('local' => 'unit', 'foreign' => 'id'));
		$this->hasOne('Model_Discipline as Discipline', array('local' => 'discipline', 'foreign' => 'id'));
		$this->hasMany('Model_Assignment as Assignment', array('local' => 'id', 'foreign' => 'person'));
		$this->hasMany('Model_Vacation as Vacation', array('local' => 'id', 'foreign' => 'person'));
		$this->getTable()->setAttribute(Doctrine::ATTR_VALIDATE, true);
	}

	public static function getForm($id = null)
	{
		if ($id !== null) {
			$Entry = Doctrine_Query::create()
				->from(__CLASS__ . ' p')
				->leftJoin('p.Unit')
				->where('id = ?', array($id))
				->fetchOne();
		}
		$return = array(
			new Auslastung_FormField(array(
				'name' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'value' => $id === null ? null : $Entry->name,
			)),
			new Auslastung_FormField(array(
				'name' => 'unit__name',
				'type' => 'text',
				'label' => 'Unit',
				'autocomplete' => true,
				'value' => $id === null ? null : $Entry->Unit->name,
			)),
			new Auslastung_FormField(array(
				'name' => 'discipline__name',
				'type' => 'text',
				'label' => 'Disziplin',
				'autocomplete' => true,
				'value' => $id === null ? null : $Entry->Discipline->name,
			))
		);
		return $return;
	}

	public function getEndDate(DateTime $Start, $hours)
	{
		$hours = (float)$hours;
		if ($hours <= 0) throw new InvalidArgumentException('$hours must greater than zero.');
		if (Auslastung_DateHelper::isWeekend($Start)) throw new InvalidArgumentException('$Start must not be a weekend.');
		if (Auslastung_DateHelper::isHoliday($Start)) throw new InvalidArgumentException('$Start must not be a holiday.');
		$End = clone($Start);
		$Start->setTime(0, 0, 0);
		$End->setTime(23, 59, 59);
		$hoursUsed = 0;
		$hoursOnDay = null;
		$this->hoursOnDay = array();
		$usedVacationDays = array();
		while($hoursUsed < $hours) {
			if($hoursOnDay >= $this->hoursPerDay) {
				$End->modify('+1 day');
				$hoursOnDay = null;
				continue;
			}
			while(Auslastung_DateHelper::isWeekend($End)
			|| Auslastung_DateHelper::isHoliday($End)) {
				$End->modify('+1 day');
				continue 2;
			}
			$hoursOnDay = 0;
			$vacationHoursOnDay = 0;
			if($this->id !== null) {
				$Vacation = Doctrine_Query::create()
					->from('Model_Vacation v')
					->leftJoin('v.Days d')
					->where('v.person = ?', array($this->id))
					->whereNotIn('d.id', $usedVacationDays)
					->fetchOne();
				if($Vacation) {
					foreach($Vacation->Days as $VacationDay) {
						if($VacationDay->date !== $End->format('Y-m-d')) continue;
						if(in_array($VacationDay->id, $usedVacationDays)) continue;
						$usedVacationDays[] = $VacationDay->id;
						$vacationHoursOnDay += (int)$VacationDay->hours;
					}
				}
			}
            $holidayHours = Auslastung_DateHelper::getHolidayHours($End);
			$workHoursOnDay = min($hours - $hoursUsed, $this->hoursPerDay - $vacationHoursOnDay - $holidayHours);
			$hoursUsed += $workHoursOnDay;
			$hoursOnDay = $workHoursOnDay + $vacationHoursOnDay + $holidayHours;
			$this->hoursOnDay[$End->format('Y-m-d')] = $workHoursOnDay;
		}
		return $End;
	}

	public function getHoursBetweenDates(DateTime $Start, DateTime $End)
	{
		if($Start > $End) throw new InvalidArgumentException('$Start must be prior to $End');
		if(Auslastung_DateHelper::isWeekend($Start)) throw new InvalidArgumentException('$Start must not be a weekend.');
		if(Auslastung_DateHelper::isHoliday($Start)) throw new InvalidArgumentException('$Start must not be a holiday.');
		$Start->setTime(0, 0, 0);
		$CheckDate = clone($Start);
		$End->setTime(23, 59, 59);
		$hours = 0;
		$this->hoursOnDay = array();
		$usedVacationDays = array();
		while($CheckDate < $End) {
			if(Auslastung_DateHelper::isHoliday($CheckDate)
			|| Auslastung_DateHelper::isWeekend($CheckDate)) {
				$CheckDate->modify('+1 day');
				continue;
			}
			$workHoursOnDay = $this->hoursPerDay;
			if($this->id !== null) {
				$Vacation = Doctrine_Query::create()
					->from('Model_Vacation v')
					->where('person = ? AND start <= ? AND end >= ?', array($this->id, $CheckDate->format('Y-m-d'), $CheckDate->format('Y-m-d')))
					->leftJoin('v.Days')
					->fetchOne();
				if($Vacation) {
					foreach($Vacation->Days as $VacationDay) {
						if($VacationDay->date !== $CheckDate->format('Y-m-d')) continue;
						if(in_array($VacationDay->id, $usedVacationDays)) continue;
						$usedVacationDays[] = $VacationDay->id;
						$workHoursOnDay -= (int)$VacationDay->hours;
					}
				}
			}
            // Holiday?
            $workHoursOnDay -= Auslastung_DateHelper::getHolidayHours($CheckDate);

			$hours += $workHoursOnDay;
			$this->hoursOnDay[$CheckDate->format('Y-m-d')] = $workHoursOnDay;
			$CheckDate->modify('+1 day');
		}
		return $hours;
	}

	public function getHours(DateTime $Start, DateTime $End, $duration)
	{
		if ((int)$duration === 0) throw new InvalidArgumentException('$duration must be greater than 0.');
		$hours = $this->getHoursBetweenDates($Start, $End);
		$numDaysWithWorkHours = 0;
		foreach ($this->hoursOnDay as $date => $workHours) {
			if ($workHours > 0) $numDaysWithWorkHours++;
		}
		foreach ($this->hoursOnDay as $date => $workHours) {
			if ( $workHours > 0 ) $this->hoursOnDay[$date] = $duration / $numDaysWithWorkHours;
		}
	}

	public function getHoursOnDay()
	{
		if($this->hoursOnDay === null) throw new Auslastung_Exception('You need to call ' . __CLASS__ . '::getHoursBetweenDates() or ' . __CLASS__ . '::getEndDate() first.');
		return $this->hoursOnDay;
	}
}