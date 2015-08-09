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
 * Autoloader for the Auslastung namespace
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Autoloader.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * Autoloader for the Auslastung namespace
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Autoloader.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_Autoloader
{
	private static $libDir;
	private static $inited = false;

	/**
	 * Constructor
	 *
	 * @param
	 */
	public function __construct()
	{
		// Add myself to autoloaders
		self::$libDir = realpath(dirname(__FILE__) . '/..') . '/';
		spl_autoload_register(array('Auslastung_Autoloader', 'autoload'));
	}

	/**
	 * Autoloader
	 */
	public static function autoload($classname)
	{
		if (!strstr($classname, 'Auslastung_') && !strstr($classname, 'Model_')) return false;
		$file = self::$libDir . str_replace('_', '/', $classname) . '.php';
		require_once $file;
	}
}