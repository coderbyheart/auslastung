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
 * Installs the app
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Install.php 144 2011-01-02 18:58:25Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage CLI
 */

/**
 * Installs the app
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Install.php 144 2011-01-02 18:58:25Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage CLI
 */
class Auslastung_CLI_Install extends Auslastung_CLI_Base
{
	/**
	 * @see EngineRoom_CLI_IAppModule::getName()
	 */
	public function getName()
	{
		return 'Installs the app';
	}

	/**
	 * Flushes data Database. DANGEROUS!
	 */
	public function doInstall()
	{
		$connection = Doctrine_Manager::getInstance()->getCurrentConnection();
		try {
			$connection->dropDatabase();
			$connection->createDatabase();
		} catch(Doctrine_Exception $e) {
			$this->cliApp->log("Failed to drop database.");
		}

		$PDO = $connection->getDbh();
		$PDO->exec('SET FOREIGN_KEY_CHECKS = 0');
		
		foreach (glob($this->app->getConfig()->getFile('sql/*.sql')) as $sql) {
			$PDO->exec(file_get_contents($sql));
		}
		
		$PDO->exec('SET FOREIGN_KEY_CHECKS = 1');
	}
}