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
 * Convert assignment of propability 4 to vacations
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: 5.php 67 2009-05-03 14:55:02Z m $
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
$PDO = $connection->getDbh();

$result = $PDO->query("SELECT value FROM sysconfig WHERE name = 'dbversion'")->fetch(PDO::FETCH_OBJ);
if ((int)$result->value >= 5) {
	echo "Update 5 already applied.\n";
	exit;
}

echo 'Deleting assignments with end prior to start: ' . $PDO->exec('DELETE FROM assignment WHERE start > end') . "\n";
echo 'Deleting assignments with 0 duration: ' . $PDO->exec('DELETE FROM assignment WHERE duration = \'0\'') . "\n";
echo 'Update assignments with manual_end: ' . $PDO->exec('UPDATE assignment SET manual_end = NULL WHERE hpd >= 8') . "\n";

$result = $PDO->query('SELECT * FROM assignment WHERE hpd > 0 AND hpd < 8 AND manual_end IS NOT NULL');
$ids = array();
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$Assignment = new Model_Assignment;
	$Assignment->project = $row['project'];
	$Assignment->person = $row['person'];
	$Assignment->start = $row['start'];
	$Assignment->manual_end = $row['manual_end'];
	$Assignment->distribute_duration = 1;
	$Assignment->duration = $row['duration'];
	$Assignment->probability = $row['probability'];
	$Assignment->description = $row['description'];
	$Assignment->save();
	$ids[] = $row['id'];
	echo 'd';
}
$PDO->exec('DELETE FROM assignment WHERE id IN(' . join(',', $ids) . ')');
$PDO->exec('ALTER TABLE assignment DROP hpd');

Doctrine_Query::create()
	->from('Model_Assignment')
	->where('duration = \'0\'')
	->delete()
	->execute();

foreach(Doctrine_Query::create()
	->from('Model_Assignment a')
	->whereIn('a.probability', array(4, 5, 6))
	->leftJoin('a.Project')
	->execute() as $Assignment) {
	$Vacation = new Model_Vacation;
	$Vacation->person = $Assignment->person;
	$Vacation->type = $Assignment->probability - 3;
	$Vacation->start = $Assignment->start;
	if($Assignment->duration !== null) {
		$Vacation->duration = $Assignment->duration;
	} else {
		$Vacation->manual_end = $Assignment->manual_end;
	}
	$description = $Assignment->Project->name;
	if (!empty($Assignment->description)) $description .= ' / ' . $Assignment->description;
	$Vacation->description = $description;
	$Vacation->save();
	if ($Vacation->duration !== $Assignment->duration) throw new Exception('Failed to convert ' . print_r($Assignment->toArray(), true));
	$Assignment->delete();
	echo 'v';
}

Doctrine_Query::create()
	->from('Model_Probability')
	->whereIn('id', array(4, 5, 6))
	->delete()
	->execute();

$PDO->exec("INSERT INTO sysconfig (name, value) VALUES ('dbversion', '5', UTC_TIMESTAMP())");