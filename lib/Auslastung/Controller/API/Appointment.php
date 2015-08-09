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
 * Appointment controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Appointment.php 61 2009-04-19 12:25:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Appointment controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Appointment.php 61 2009-04-19 12:25:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Appointment extends Auslastung_Controller_API_Base
{
    protected $model = 'Model_Appointment';

    protected function addEntry()
    {
        if (!$this->hasInputErrors()) {
            $Appointment = new Model_Appointment;
            $Appointment->organization = Auslastung_Session::getInstance()->getOrganizationId();
            list($startDate, $endDate) = $this->getDates();
            $Appointment->start = strftime('%Y-%m-%d %H:%M:%S', $startDate->format('U'));
            $Appointment->end = strftime('%Y-%m-%d %H:%M:%S', $endDate->format('U'));
            $Appointment->description = $this->Request->getInput('description');
            $Appointment->is_holiday = (boolean)$this->Request->getInput('is_holiday');
            $Appointment->save();
            $this->Response->setResult($Appointment->toArray(), 1);
        }
    }

    protected function getDates()
    {
        $startTime = $this->Request->getInput('start_time');
        if (strlen($startTime) === 2) $startTime .= ':00';
        if (empty($startTime)) {
            $startTime = '00:00';
        }
        $startTime .= ':00';
        $startDate = new DateTime(strftime('%Y-%m-%d %H:%M:%S', strtotime($this->Request->getInput('day') . ' ' . $startTime)));

        $endDate = clone $startDate;
        $endTime = $this->Request->getInput('end_time');
        if (strlen($endTime) === 2) $endTime .= ':00';
        if (empty($endTime)) {
            $endTime = '00:00';
        }
        $endTime .= ':00';
        list($hour, $minute, $seconds) = explode(':' , $endTime);
        $endDate->setTime($hour, $minute, $seconds);
        if ($endTime === '00:00:00') {
            $endDate->modify('+1 day');
        }
        return array($startDate, $endDate);
    }

    protected function updateEntry()
    {
        $this->Entry->organization = Auslastung_Session::getInstance()->getOrganizationId();
        list($startDate, $endDate) = $this->getDates();
        $this->Entry->start = strftime('%Y-%m-%d %H:%M:%S', $startDate->format('U'));
        $this->Entry->end = strftime('%Y-%m-%d %H:%M:%S', $endDate->format('U'));
        $this->Entry->description = $this->Request->getInput('description');
        $this->Entry->is_holiday = (boolean)$this->Request->getInput('is_holiday');
        $this->Entry->save();
        $this->Response->setResult($this->Entry->toArray(), 1);
    }
}
