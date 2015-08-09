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
 * Controller for Auslastung_DateHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Datehelper.php 55 2009-04-13 18:59:06Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Controller for Auslastung_DateHelper
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Datehelper.php 55 2009-04-13 18:59:06Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Datehelper implements Auslastung_Controller_Base
{

	public function __construct( Auslastung_Request $Request )
	{
		switch($Request->getParam('id')) {
		case 'getHoursBetweenDates':
			$this->Response = new Auslastung_Response_JSON();
			if($Request->getInput('person') !== null) {
				$Person = Doctrine::getTable('Model_Person')->find($Request->getInput('person'));
				$this->Response->setResult($Person->getHoursBetweenDates(new DateTime($Request->getInput('start')), new DateTime($Request->getInput('end'))));
			} else {
				$this->Response->setResult(Auslastung_DateHelper::getHoursBetweenDates(new DateTime($Request->getInput('start')), new DateTime($Request->getInput('end'))));
			}
			break;
		case 'getWorkingDaysBetweenDates':
			$this->Response = new Auslastung_Response_JSON();
			$this->Response->setResult(Auslastung_DateHelper::getWorkingDaysBetweenDates(new DateTime($Request->getInput('start')), new DateTime($Request->getInput('end'))));
			break;
		case 'getEndDate':
			$this->Response = new Auslastung_Response_JSON();
			$this->Response->setResult(Auslastung_DateHelper::getEndDate(new DateTime($Request->getInput('start')), $Request->getInput('hours'))->format('Y-m-d'));
			break;
		default:
			throw new Auslastung_Exception('Invalid action: ' . $Request->getParam('id'));
		}
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
