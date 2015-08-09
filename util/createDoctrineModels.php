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
 * Refreshes database model classes
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: createDoctrineModels.php 2 2008-12-19 07:54:52Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Frontend
 */
define('DEVELOPER', true);

/**
 * include config
 */
require_once dirname(__FILE__) . '/../lib/config.php';

/**
 * include base class w/ autoloader
 */
require_once HOME . 'external/doctrine/Doctrine.php';
spl_autoload_register(array('Doctrine', 'Autoload'));

$config = parse_ini_file(HOME . 'config.ini');
Doctrine_Manager::connection($config['dsn']);
Doctrine::generateModelsFromDb(HOME . 'var/doctrine', array(), array('phpDocPackage' => 'Auslastung', 'phpDocSubpackage' => 'Model', 'phpDocName' => 'Markus Tacker', 'phpDocEmail' => 'm@tacker.org'));
echo "All done.\n";
