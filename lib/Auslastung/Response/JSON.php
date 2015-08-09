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
 * Represents a JSON response
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: JSON.php 2 2008-12-19 07:54:52Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Response
 */
 
/**
 * Represents a JSON response
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: JSON.php 2 2008-12-19 07:54:52Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Response
 */
class Auslastung_Response_JSON implements Auslastung_Response_Base
{
	const STATUS_OK = 'OK';
	const STATUS_FAILED = 'FAILED';
	const USER_FAIL = 'USERFAIL';
	const SYSTEM_FAIL = 'SYSTEMFAIL';
	
	protected $result;
	protected $status = self::STATUS_OK;
	protected $code = 0;
	protected $length = 0;
	protected $systemFail = false;
	protected $userFail = false;
	protected $message;
	
	/**
	 * Constructor
	 *
	 * @param mixed Result
	 */
	public function __construct( $result = null )
	{
		$this->setResult( $result );
	}
	
	/**
	 * Set response result
	 *
	 * @param mixed Result
	 * @param int Lenght of result
	 */
	public function setResult( $result, $length = null )
	{
		$this->result = $result;
		if ( $length !== null ) $this->length = $length;
	}
	 
	/**
	 * Set response status
	 *
	 * @param string Status
	 * @param int Status code
	 */
	 public function setStatus( $status, $code = null, $message = null, $failtype = self::SYSTEM_FAIL )
	{
		if ( !in_array( $status, array( self::STATUS_OK, self::STATUS_FAILED ) ) ) throw new Auslastung_Exception( 'Invalid status: ' . $status );
		if ( $code !== null && ( !is_int( $code ) || $code < 0 ) ) throw new Auslastung_Exception( 'Invalid status code: ' . $code );
		$this->status = $status;
		if ( $code !== null ) $this->code = $code;
		if ( $message !== null ) $this->message = $message;
		if ( $status === self::STATUS_FAILED ) {
			if ( $failtype === self::USER_FAIL ) {
				$this->userFail = true;
			} else {
				$this->systemFail = true;
			}
		}
	}
	
	/**
	* Send response to client
	*/
	public function send()
	{
		header('Content-Type: application/json; charset=UTF-8');
		$return = new stdClass;
		$return->status = $this->status;
		$return->code = $this->code;
		$return->message = $this->message;
		$return->length = $this->length;
		$return->userFail = $this->userFail;
		$return->systemFail = $this->systemFail;
		$return->result = $this->result;
		echo json_encode( $return );
		unset( $return );
	}
}
