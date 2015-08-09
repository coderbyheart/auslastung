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
 * A request
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Request.php 10 2009-02-24 12:32:07Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Dispatcher
 */
 
/**
 * A request
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Request.php 10 2009-02-24 12:32:07Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Dispatcher
 */
class Auslastung_Request extends ezcUrl
{
	protected $method;
	
	public function __construct()
	{
		if ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) ) $this->method = $_SERVER[ 'REQUEST_METHOD' ]; 
		$RequestConfig = new ezcUrlConfiguration();
		$RequestConfig->basedir = 'api';
		$RequestConfig->addOrderedParameter( 'ressource' );
		$RequestConfig->addOrderedParameter( 'id' );
		$RequestConfig->addOrderedParameter( 'subressource' );
		parent::__construct( $_SERVER[ 'REQUEST_URI' ], $RequestConfig );
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function getInput( $name, $filter = null, $filterOptions = null, $from = null )
	{
		if ( $filter === null ) $filter = FILTER_SANITIZE_STRING;
		if ( $filterOptions === null ) $filterOptions = FILTER_FLAG_NO_ENCODE_QUOTES;
		if ( $from === null ) $from = $this->method;
		switch( $from ) {
		case 'GET':
			return filter_input( INPUT_GET, $name, $filter, $filterOptions ); 
			break;
		case 'POST':
			return filter_input( INPUT_POST, $name, $filter, $filterOptions ); 
			break;
		}
	}
}
