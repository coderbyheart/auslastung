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
 * FormField
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: FormField.php 9 2009-02-24 12:10:25Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
 
/**
 * FormField
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: FormField.php 9 2009-02-24 12:10:25Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
 class Auslastung_FormField
 {
	 public $name;
	 public $label;
	 public $type;
	 public $values = array();
	 public $value;
	 public $autocomplete = false;
	 public $extra;
	 
	 public function __construct( array $params )
	 {
		 foreach( $params as $k => $v ) $this->$k = $v;
		 if ( $this->name === null ) $this->name = md5( uniqid( __CLASS__ ) ) . '_';
		 if ( $this->type === null ) $this->type = 'text';
		 if ( $this->label === null ) $this->label = $this->name;
	 }
 }
