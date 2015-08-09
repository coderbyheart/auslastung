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
 * Frontends entry point
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: index.php 137 2011-01-02 12:54:35Z m $
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
 * @todo TODO FIXME Detect unit test requests
 */
new Auslastung_App_Frontend();
