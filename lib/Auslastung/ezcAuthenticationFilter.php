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
 * Autnenticate against doctrine
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: ezcAuthenticationFilter.php 138 2011-01-02 17:21:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Utils
 */

/**
 * Autnenticate against doctrine
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: ezcAuthenticationFilter.php 138 2011-01-02 17:21:21Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Utils
 */
class Auslastung_ezcAuthenticationFilter extends ezcAuthenticationFilter
{
	protected $User;

	/**
     * @var int Authentication failed
     */
    const STATUS_FAILED = 1;

    /**
     * @var int Missing credentials
     */
    const STATUS_MISSING_CREDENTIALS = 2;

    /**
     * Runs the filter and returns a status code when finished.
     *
     * @param ezcAuthenticationCredentials $credentials Authentication credentials
     * @return int
     */
    public function run($credentials)
    {
		if ($credentials->id === null || $credentials->password === null) return false;
		$User = Doctrine_Query::create()
			->from('Model_User u')
			->leftJoin('u.Organizations o')
			->leftJoin('u.Config uc')
			->leftJoin('o.Config oc')
			->where('u.is_active = \'1\' AND u.email = ? AND u.password = ?', array($credentials->id, $credentials->password))
			->fetchOne();
		if (!$User) return false;
		$User->z_ts_last_login = Auslastung_Environment::getTimestamp();
		$User->save();
		$this->User = $User;
		return ezcAuthenticationFilter::STATUS_OK;
    }

    public function getUser()
    {
		return $this->User;
	}
}