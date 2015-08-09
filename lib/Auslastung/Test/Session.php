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
 * The session (for testing)
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Session.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 */

/**
 * The session (for testing)
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Session.php 137 2011-01-02 12:54:35Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 */
class Auslastung_Test_Session extends Auslastung_Session
{
    private $User;

    public function __construct()
    {
        // Allowed
    }

    /**
     * @param Model_User $User
     */
    public function setUser(Model_User $User)
    {
        $this->User = $User;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->User->id;
    }
}