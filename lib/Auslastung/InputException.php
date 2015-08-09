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
 * Exception used for input errors
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: InputException.php 6 2009-01-02 23:12:30Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
 
/**
 * Exception used for input errors
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: InputException.php 6 2009-01-02 23:12:30Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
 class Auslastung_InputException extends Auslastung_Exception 
 {
	 protected $field;
	 
	 public function __construct( $field )
	 {
		 $this->field = $field;
		 parent::__construct( 'The data entered for the field "' . $field . '" contained errors.' );
	 }
	 
	 public function getField()
	 {
		 return $this->field;
	 }
 }
