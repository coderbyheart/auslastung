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
 * API entry point
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: api.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Frontend
 */

/**
 * include base class
 */
require_once '../lib/Auslastung/Autoloader.php';
new Auslastung_Autoloader();

/**
 * Handle current request
 */
try {
	if (isset($_GET['usetestdb'])) {
		$app = new Auslastung_App_API(Auslastung_App_Options::create()->isTest(true));
	} else {
		$app = new Auslastung_App_API();
	}	
} catch( Auslastung_Exception_User $E ) {
	if ($app && $app->getConfig()->isDeveloper()) FirePHP::getInstance()->error($E);
	$Response = new Auslastung_Response_JSON();
	$Response->setStatus( Auslastung_Response_JSON::STATUS_FAILED, $E->getCode(), $E->getMessage(), Auslastung_Response_JSON::USER_FAIL );
	$Response->send();
} catch( Exception $E ) {
    $Response = new Auslastung_Response_JSON();
    $Response->setStatus( Auslastung_Response_JSON::STATUS_FAILED, $E->getCode(), $E->getMessage(), Auslastung_Response_JSON::SYSTEM_FAIL );
    $Response->send();
}