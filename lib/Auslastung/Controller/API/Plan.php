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
 * Plan controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Plan.php 185 2012-01-02 10:56:41Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Plan controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Plan.php 185 2012-01-02 10:56:41Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Plan implements Auslastung_Controller_Base
{
	protected $Response;

	public function __construct( Auslastung_Request $Request )
	{
		$this->Response = new Auslastung_Response_JSON();
		$date = $Request->getInput( 'date', FILTER_VALIDATE_REGEXP, array( 'options' => array( 'regexp' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/' ) ) );
		if( $date !== null ) {
			$startTime = new DateTime( $date );
		} else {
			$startTime = new DateTime();
		}
		$startTime->setTime( 0, 0, 0 );
		while( (int)$startTime->format( 'N' ) !== 1 ) {
			$startTime->modify( '-1 days' );
		}

		$plan = array();
		$plan[ 'weeknumber' ] = strftime( '%V', $startTime->format( 'U' ) );
		$plan[ 'startdate' ] = strftime( '%Y-%m-%d', $startTime->format( 'U' ) );
		$plan[ 'year' ] = strftime( '%Y', $startTime->format( 'U' ) );
		// Create days
        $holidayduration = array();
		for ( $i = 0; $i < 5; $i++ ) {
			$date = clone( $startTime );
			$date->modify( '+' . $i . ' days' );
            $ts = strftime( '%Y-%m-%d', $date->format( 'U' ) );
			$plan[ 'days' ][] = array(
				'date' => strftime( '%a. %d. %b. \'%y', $date->format( 'U' ) ),
				'day' => $i + 1,
				'ts' => $ts,
                'appointments' => array(),
			);
            $holidayduration[$ts] = 0;
			if ( $i === 0 ) $startts = strftime( '%Y-%m-%d', $date->format( 'U' ) );
			if ( $i === 4 ) $endts = strftime( '%Y-%m-%d', $date->format( 'U' ) );
		}

		// Appointments
        $colorDefault = Auslastung_Session::getInstance()->getOrganizationConfig(Model_Appointment::COLOR_DEFAULT_CONFIG);
        if ($colorDefault === null) $colorDefault = Model_Appointment::COLOR_DEFAULT;
        $textcolorDefault = Auslastung_Session::getInstance()->getOrganizationConfig(Model_Appointment::TEXTCOLOR_DEFAULT_CONFIG);
        if ($textcolorDefault === null) $textcolorDefault = Model_Appointment::TEXTCOLOR_DEFAULT;
        $colorHoliday = Auslastung_Session::getInstance()->getOrganizationConfig(Model_Appointment::COLOR_HOLIDAY_CONFIG);
        if ($colorHoliday === null) $colorHoliday = Model_Appointment::COLOR_HOLIDAY;
        $textcolorHoliday = Auslastung_Session::getInstance()->getOrganizationConfig(Model_Appointment::TEXTCOLOR_HOLIDAY_CONFIG);
        if ($textcolorHoliday === null) $textcolorHoliday = Model_Appointment::TEXTCOLOR_HOLIDAY;

		foreach( Doctrine_Query::create()
			->from('Model_Appointment a')
			->addWhere('a.start >= ?', array($startts . ' 00:00:00'))
			->addWhere('a.start <= ?', array($endts . ' 23:59:59'))
            ->addWhere('a.organization = ?', array(Auslastung_Session::getInstance()->getOrganizationId()))
            ->leftJoin('a.Author p')
			->execute() as $Appointment ) {
			foreach($plan['days'] as $k => $v) {
				if ($v['ts'] === substr($Appointment->start, 0, 10)) {
                    $wholeDay = (int)$Appointment->duration >= 8;
                    $isHoliday = (boolean)$Appointment->is_holiday;
					$plan['days'][$k]['appointments'][] = array(
                        'id' => $Appointment->id,
                        'start' => $Appointment->start,
                        'end' => $Appointment->end,
                        'is_holiday' => $isHoliday,
                        'duration' => (int)$Appointment->duration,
                        'description' => $Appointment->description,
                        'start_time' => $wholeDay ? null : substr($Appointment->start, 11, 5),
                        'end_time' => $wholeDay ? null : substr($Appointment->end, 11, 5),
                        'author' => array('name' => $Appointment->Author->name, 'isme' => $Appointment->Author->id == Auslastung_Session::getInstance()->getUserId()),
                        'color' => $isHoliday ? $colorHoliday : $colorDefault,
                        'textcolor' => $isHoliday ? $textcolorHoliday : $textcolorDefault,
                    );
                    // Wenn es mehr als 8 Stunden Feiertagstermine an diesem Tag gibt, ist der Tag ein Feiertag.
                    if ($Appointment->is_holiday) {
                        $holidayduration[$v['ts']] += $Appointment->duration;
                        if ($holidayduration[$v['ts']] >= 8) $plan['days'][$k]['is_holiday'] = true;
                    }
				}
			}
		}
        // Update holiday state of day
        foreach($plan['days'] as $k => $v) {
            $holidayduration = 0;
            foreach($v['appointments'] as $appointment) {
                if (!$appointment['is_holiday']) continue;
                $holidayduration += (int)$appointment['duration'];
            }
        }

		// Vacation
		$vacDayHours = array();
		foreach(Doctrine_Query::create()
			->from('Model_Vacation v')
			->leftJoin('v.Person p')
			->leftJoin('v.Type t')
			->addWhere('p.organization = ?', array(Auslastung_Session::getInstance()->getOrganizationId()))
			->addWhere('v.start <= ?', array($endts))
			->addWhere('v.end >= ?', array($startts))
			->execute() as $Vacation) {
			$vac = new stdClass;
			$vac->id = $Vacation->id;
			$vac->type = $Vacation->Type->name;
			$vac->color = $Vacation->Type->color;
			$vac->textcolor = $Vacation->Type->textcolor;
			$vac->person = $Vacation->person;
			$vac->description = $Vacation->description;
			$vac->duration = $Vacation->duration;
			$vac->start = $Vacation->start;
			$vac->end = $Vacation->end;
			$vac->days = array();
			$startDate = new DateTime($Vacation->start);
			$endDate   = new DateTime($Vacation->end);
			$i = 0;
			$diffDays = ($startDate >= $startTime) ? 0 : Auslastung_DateHelper::getWorkingDaysBetweenDates($startDate, $startTime);
			foreach($plan['days'] as $planDay) {
				$PlanDayDate = new DateTime($planDay['ts']);
				$dayInfo = array(
					'ts' => $planDay['ts'],
					'day' => $i,
					'hours' => $Vacation->getHoursOnDay($PlanDayDate),
				);
				if($dayInfo['hours'] > 0) $vac->days[] = $dayInfo;
				$i++;
			}
			// Author
			if ($Vacation->author !== null) {
				$vac->author = array(
					'name' => $Vacation->Author->name,
					'isme' => $Vacation->Author->id == Auslastung_Session::getInstance()->getUserId()
				);
			} else {
				$vac->author = null;
			}
			$plan['vacations'][] = $vac;
		}

		// Assignments
		foreach( Doctrine_Query::create()
			->from( 'Model_Assignment a' )
			->leftJoin( 'a.Project p' )
			->leftJoin( 'a.Probability pr' )
			->leftJoin( 'a.Days d' )
			->addWhere( 'p.organization = ?', array( Auslastung_Session::getInstance()->getOrganizationId() ) )
			->addWhere( 'a.start <= ?', array( $endts ) )
			->addWhere( 'a.end >= ?', array( $startts ) )
			->orderBy( 'pr.percentage DESC' )
			->execute() as $Assignment ) {
			$ass = new stdClass;
			$ass->id = $Assignment->id;
			$ass->person = $Assignment->person;
			$ass->title = $Assignment->Project->name;
			$ass->description = $Assignment->description;
			$ass->color = $Assignment->Probability->color;
			$ass->textcolor = $Assignment->Probability->textcolor;
			$ass->duration = $Assignment->duration;
			$ass->start = $Assignment->start;
			$ass->end = $Assignment->end;
            $ass->is_homeoffice = $Assignment->is_homeoffice;
			$ass->days = array();
			$startDate = new DateTime( $Assignment->start );
			$endDate   = new DateTime( $Assignment->end );
			$i = 0;
			foreach($plan['days'] as $planDay) {
				$PlanDayDate = new DateTime($planDay['ts']);
				$dayInfo = array(
					'ts' => $planDay['ts'],
					'day' => $i,
					'hours' => $Assignment->getHoursOnDay($PlanDayDate),
				);
				if($dayInfo['hours'] > 0) $ass->days[] = $dayInfo;
				$i++;
			}
			// Author
			if ($Assignment->author !== null) {
				$ass->author = array(
					'name' => $Assignment->Author->name,
					'isme' => $Assignment->Author->id == Auslastung_Session::getInstance()->getUserId()
				);
			} else {
				$ass->author = null;
			}
			$plan['assignments'][] = $ass;
		}

		// Done
		$this->Response->setResult( $plan, count( $plan ) );
	}

	/**
	* Get response of controller
	* @return Auslastung_Response
	*/
	public function getResponse()
	{
		return $this->Response;
	}
}
