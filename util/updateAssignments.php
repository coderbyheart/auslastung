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
 * Update all assignments (to reflect new changes in logic)
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: updateAssignments.php 54 2009-04-08 17:38:35Z m $
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
require_once LIB . 'Auslastung.php';

/**
 * include base class w/ autoloader
 */
require_once HOME . 'external/doctrine/Doctrine.php';
spl_autoload_register(array('Doctrine', 'Autoload'));

$config = parse_ini_file(HOME . 'config.ini');
$DoctrineManager = Doctrine_Manager::getInstance();
$connection = $DoctrineManager->openConnection($config['dsn']);
$connection->setCharset('UTF8');

foreach(Doctrine_Query::create()
	->from('Model_Vacation')
	->execute() as $Vacation) {
	$Vacation->save();
	echo 'v';
}

foreach(Doctrine_Query::create()
	->from('Model_Assignment')
	->execute() as $Assignment) {
	$Assignment->save();
	echo 'a';
}