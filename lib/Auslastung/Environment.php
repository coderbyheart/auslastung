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
 * Environment class, provides some small globally used features
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Environment.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * Environment class, provides some small globally used features
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Environment.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_Environment
{
	private static $ts;
	private static $tracUrl;
	private static $svnrev;
	private static $name;
    /**
     * @var string
     */
    private static $baseHref;
    /**
     * @var bool
     */
    private static $isTest = false;
	
	/**
	 * @return current utc timestamp (i.e. 2010-09-11 22:50:12)
	 */
	public static function getTimestamp()
	{
		if (self::$ts === null) {
			$UTC = new DateTime('now', new DateTimeZone('UTC'));
			self::$ts = $UTC->format('Y-m-d H:m:s');
		}		
		return self::$ts;
	}
	
	public static function setSvnRev($svnrev)
	{
		self::$svnrev = $svnrev;
	}
	
	public static function getSvnRev()
	{
		return self::$svnrev;
	}
	
	public static function setTracUrl($tracUrl)
	{
		self::$tracUrl = $tracUrl;
	}
	
	public static function getTracUrl()
	{
		return self::$tracUrl;
	}
	
	public static function setName($name)
	{
		self::$name = $name;
	}
	
	public static function getName()
	{
		return self::$name;
	}

    /**
     * @param string $baseHref
     */
    public static function setBaseHref($baseHref)
    {
        self::$baseHref = $baseHref;
    }

    /**
     * @return string
     */
    public static function getBaseHref()
    {
        return self::$baseHref;
    }

    /**
     * @param boolean $isTest
     */
    public static function setIsTest($isTest)
    {
        self::$isTest = (boolean)$isTest;
    }

    /**
     * @return boolean
     */
    public static function isTest()
    {
        return self::$isTest;
    }
}