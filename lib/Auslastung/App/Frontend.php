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
 * Frontend App
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Frontend.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage App
 */

/**
 * Frontend App
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Frontend.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage App
 */
class Auslastung_App_Frontend extends Auslastung_App_Base
{
	/**
	 * Constructor
	 *
	 * @param Auslastung_App_Options Options
	 */
	public function __construct(Auslastung_App_Options $options = null)
	{
		parent::__construct($options);
		$Response = new Auslastung_Response_HTML($this);
		$Response->send();
	}
}