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
 * Frontend App
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: API.php 174 2011-12-30 20:13:33Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage App
 */

/**
 * Frontend App
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: API.php 174 2011-12-30 20:13:33Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage App
 */
class Auslastung_App_API extends Auslastung_App_Base
{
	private $session;

	/**
	 * Constructor
	 *
	 * @param Auslastung_App_Options Options
	 */
	public function __construct(Auslastung_App_Options $options = null)
	{
		parent::__construct($options);

		$this->session = Auslastung_Session::create($this->getVersion()); 

		$Request = new Auslastung_Request();
		switch($Request->getParam('ressource')) {
		case 'clock':
		case 'organization':
		case 'plan':
		case 'person':
		case 'user':
		case 'unit':
		case 'discipline':
		case 'assignment':
		case 'vacation':
        case 'appointment':
		case 'project':
		case 'datehelper':
		case 'login':
		case 'lostpassword':
		case 'logout':
		case 'calendar':
			$model = 'Model_' . ucfirst($Request->getParam('ressource'));
			if ($Request->getParam('id') === 'form') {
				$Result = array(
					'fields' => call_user_func(array($model, 'getForm'), $Request->getInput('id'), $Request->getInput('type')),
					'deleteable' => call_user_func(array($model, 'isDeleteAble')),
				);
				$Response = new Auslastung_Response_JSON();
				$Response->setResult($Result);
			} elseif ($Request->getParam('id') === 'autocompleter') {
				$result = Doctrine_Query::create()
					->select('id, name')
					->from('Model_' . ucfirst($Request->getParam('ressource')))
					->where('name LIKE ?', array('%' . $Request->getInput('search') . '%'))
					->fetchArray();
				$Response = new Auslastung_Response_JSON();
				$Response->setResult($result, count($result));
			} else {
				$class = 'Auslastung_Controller_API_' . ucfirst($Request->getParam('ressource'));
				try {
					$Controller = new $class($Request);
					if ($Controller instanceof Auslastung_Controller_API_Base) {
						if ($Controller->hasInputErrors()) {
							$fields = $Controller->getInputErrors();
							$Response = new Auslastung_Response_JSON();
							$Response->setResult($fields, count($fields));
							$Response->setStatus(Auslastung_Response_JSON::STATUS_FAILED, 1, 'Die eingegebenen Daten enthielten Fehler.', Auslastung_Response_JSON::USER_FAIL);
						} else {
							$Response = $Controller->getResponse();
						}
					} else {
						$Response = $Controller->getResponse();
					}
				} catch(Doctrine_Validator_Exception $E) {
					$fields = array();
					foreach($E->getInvalidRecords() as $ErrorRecord) {
						foreach($ErrorRecord->getErrorStack() as $k => $v) {
							if (get_class($ErrorRecord) !== $model) {
								$fields[] = $ErrorRecord->getTable()->tableName . '__' . $k;
							} else {
								$fields[] = $k;
							}
						}
					}
					$Response = new Auslastung_Response_JSON();
					$Response->setResult($fields, count($fields));
					$Response->setStatus(Auslastung_Response_JSON::STATUS_FAILED, 1, 'Die eingegebenen Daten enthielten Fehler.', Auslastung_Response_JSON::USER_FAIL);
				}
			}
			break;
		default:
			throw new Auslastung_Exception('Invalid ressource: ' . $Request->getParam('ressource'));
		}
		$Response->send();
	}
}