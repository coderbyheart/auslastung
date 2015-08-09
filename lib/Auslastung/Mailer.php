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
 * Send emails
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Mailer.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */

/**
 * Send emails
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Mailer.php 164 2011-12-28 17:29:08Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage System
 */
class Auslastung_Mailer
{
	private $mail;
	private $subject = '';
	private $body = '';
	private $recipients = array();
	private $send = true;

	public function __construct()
	{
		$this->mail = new ezcMailComposer();
		$developer = new ezcMailAddress('m@tacker.org', 'Markus Tacker');
		$app = $developer;
		$this->mail->from = $app;
		$this->mail->addBcc($developer);
	}

	/**
	 * Add recipient
	 *
	 * @param Model_User recipient
	 */
 	public function addTo(Model_User $recipient)
 	{
 		$this->recipients[] = $recipient;
 		$this->mail->addTo(new ezcMailAddress($recipient->email, $recipient->name));
 		// Dont send mail to .invalid domains
 		if (strstr($recipient->email, '.invalid')) $this->send = false;
 	}

	/**
	 * Sets the subject
	 *
	 * @param string subject
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	/**
	 * Sets the body
	 *
	 * @param string body
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}

	/**
	 * Sends the email
	 */
 	public function send()
 	{
 		if (!$this->send) return;
 		$this->mail->subject = '[' . Auslastung_Environment::getName() . '] ' . $this->subject;

 		$body = '';

 		foreach ($this->recipients as $Recipient) $body .= 'Hallo ' . $Recipient->name . ",\n";

 		$body .= "\n" . $this->body . "\n\n";
	 	$body .= str_repeat('_', 72) . "\n\n";
	 	$body .= Auslastung_Environment::getName() . ' r' . Auslastung_Environment::getSvnRev() . ' · ';
		$body .= Auslastung_Environment::getBaseHref();
		$body .= "\n";
		$body .= "Probleme oder Feedback? Hier gehts zum Bugtracker: " . Auslastung_Environment::getTracUrl() . "\n";
		$body .= "\n";
		$body .= "-- \n";
		$body .= Auslastung_Environment::getName() . ' wird entwickelt von Markus Tacker | http://coderbyheart.de/';

 		$this->mail->plainText = $body;
 		$this->mail->build();
        if (Auslastung_Environment::isTest()) return;
		$transport = new ezcMailMtaTransport();
		$transport->send($this->mail);
 	}
}