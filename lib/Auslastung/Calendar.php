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
 * DateHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Calendar.php 34 2009-03-15 21:37:28Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
 
/**
 * DateHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Calendar.php 34 2009-03-15 21:37:28Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_Calendar
{
	protected $startDate;
	
	public function __construct(DateTime $startDate)
	{
		$this->startDate = $startDate;
		$this->startDate->setDate((int)$this->startDate->format('Y'), (int)$this->startDate->format('m'), 1);
	}
	
	public function getMonth()
	{
		$nextDate = clone($this->startDate);
		$nextDate->modify('+1 month');
		$prevDate = clone($this->startDate);
		$prevDate->modify('-1 month');
		$return = array(
			'month' => $this->startDate->format('Y-m'),
			'label' => strftime('%B %Y', $this->startDate->format('U')),
			'next' => $nextDate->format('Y-m-d'),
			'previous' => $prevDate->format('Y-m-d'),
			'weekdays' => array(),
			'weeks' => array(),
		);
		// lokalisierte Wochentage
		$WeekDate = clone($this->startDate);
		while((int)$WeekDate->format('N') !== 1) {
			$WeekDate->modify('-1 day');
		}
		$CalDate = clone($WeekDate);
		$CalEnd = clone($this->startDate);
		$CalEnd->modify('+1 month');
		do{
			$return['weekdays'][] = strftime('%a', $WeekDate->format('U'));
			$WeekDate->modify('+1 day');
		} while((int)$WeekDate->format('N') !== 1);
		// Wochen
		$nWeek = 0;
		do {
			$return['weeks'][$nWeek][] = array(
				'label' => (string)$CalDate->format('j'),
				'date' => (string)$CalDate->format('Y-m-d'),
				'weekend' => (int)$CalDate->format('N') > 5,
				'holiday' => Auslastung_DateHelper::isHoliday($CalDate),
			);
			$CalDate->modify('+1 day');
			if ((int)$CalDate->format('N') === 1) $nWeek++;
			$cond = (int)$CalDate->format('N') !== 1 || $CalDate < $CalEnd;
		} while($cond);
		return $return;
	}
}