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
 * Model for Holidays
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Holiday.php 173 2011-12-30 19:26:09Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
 
/**
 * Model for Holidays
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Holiday.php 173 2011-12-30 19:26:09Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Model
 */
class Model_Holiday extends Model_Appointment
{
    protected $is_holiday = true;
}