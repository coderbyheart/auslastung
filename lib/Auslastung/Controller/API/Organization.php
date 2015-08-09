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
 * Organization controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Organization.php 172 2011-12-30 16:26:27Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */

/**
 * Organization controller
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Organization.php 172 2011-12-30 16:26:27Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Controller
 */
class Auslastung_Controller_API_Organization extends Auslastung_Controller_API_Base
{
	protected $model = 'Model_Organization';

	public function __construct(Auslastung_Request $Request)
	{
		if (!$this->getAssignment()) throw new Auslastung_Exception_Permission('Permission denied.');

		$this->Request = $Request;

		// Team-Autocompleter
		if ($Request->getParam('id') === 'team' && $Request->getParam('subressource') === 'autocompleter') {
			$this->getTeamAutocompleter();
			return;
		}

		$id = (int)$Request->getParam('id');
		if ( $id !== (int)Auslastung_Session::getInstance()->getOrganizationId() ) throw new Auslastung_Exception_Permission('Permission denied.');

		switch($Request->getMethod()) {
		case 'GET':
			switch($Request->getParam('subressource')) {
			case 'feed':
				$this->getFeed($id);
				break;
			case 'team':
				$this->getTeam($id);
				break;
			default:
				$this->getPersons($id);
			}
			break;
		case 'POST':
			switch($Request->getParam('subressource')) {
			case 'team':
				if ($Request->getInput('method') === 'delete'
				|| $Request->getInput( 'method', null, null, 'GET' ) === 'delete' ) {
					$this->removeTeamMember($id);
				} else {
					$this->addTeamMember($id);
				}
				break;
			default:
				parent::__construct($Request);
			}
			break;
		}
	}

	/**
	 * @param int ID
	 * @return Model_Abstract
	 */
	protected function getEntryById($id)
	{
		return parent::getEntryById( Auslastung_Session::getInstance()->getOrganizationId() );
	}

	protected function getPersons($id)
	{
		$this->Response = new Auslastung_Response_JSON();

		// Load Persons
		$result = array();
		foreach( Doctrine_Query::create()
			->from( 'Model_Person p' )
			->leftJoin( 'p.Unit u' )
			->leftJoin( 'p.Discipline d' )
			->where( 'p.organization = ?', $id )
			->orderBy( 'u.name ASC, d.name ASC, p.name ASC' )
			->execute() as $Person ) {
			if (!isset($result[$Person->Unit->id])) {
				$result[$Person->Unit->id] = $Person->Unit->toArray();
				$result[$Person->Unit->id]['disciplines'] = array();
			}
			if (!isset($result[$Person->Unit->id]['disciplines'][$Person->Discipline->id])) {
				$result[$Person->Unit->id]['disciplines'][$Person->Discipline->id] = $Person->Discipline->toArray();
				$result[$Person->Unit->id]['disciplines'][$Person->Discipline->id]['people'] = array();
			}
			$result[$Person->Unit->id]['disciplines'][$Person->Discipline->id]['people'][$Person->id] = array(
				'id' => $Person->id,
				'name' => $Person->name,
			);
		}

		// Remove ids from array
		$result = array_values($result);
		foreach($result as $rk => $unit) {
			$result[$rk]['disciplines'] = array_values($unit['disciplines']);
			foreach($result[$rk]['disciplines'] as $dk => $discipline) {
				$result[$rk]['disciplines'][$dk]['people'] = array_values($discipline['people']);
			}
		}

		$this->Response->setResult( $result, count( $result ) );
	}

	protected function getTeam($id)
	{
		$return = array();

		// Owner
		$OwnerResult = Doctrine_Query::create()
			->select('o.id, u.name, u.email')
			->from('Model_Organization o')
			->leftJoin('o.Owner u')
			->where('o.id = ?', array($id))
			->fetchOne(array(), Doctrine::HYDRATE_ARRAY);

		// Team
		$userId = Auslastung_Session::getInstance()->getUserId();
		foreach(Doctrine_Query::create()
			->select('x.*, u.name, u.email')
			->from('Model_XUserOrganization x')
			->where('organization = ?', $id)
			->leftJoin('x.User u')
			->orderBy('u.name ASC')
			->execute(array(), Doctrine::HYDRATE_ARRAY) as $OrgaUsers) {
				$return[] = array_merge(array('owner' => $OrgaUsers['User']['id'] === $OwnerResult['Owner']['id'], 'you' => (int)$OrgaUsers['User']['id'] === $userId), $OrgaUsers['User']);
			}
		$this->Response = new Auslastung_Response_JSON();
		$this->Response->setResult($return, count($return));
	}

	protected function getFeed($id)
	{
		$return = array();

		$ts = filter_var($this->Request->getInput('ts'), FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/')));

		$Q = Doctrine_Query::create()
			->select('f.action, f.z_ts_created, o.name, s.name')
			->from('Model_OrganizationFeed f')
			->where('organization = ?', $id)
			->leftJoin('f.Operator o')
			->leftJoin('f.Subject s')
			->orderBy('f.z_ts_created DESC')
			->where('f.operator != ?', Auslastung_Session::getInstance()->getUserId())
			->limit(10);
		if ($ts) $Q->addWhere('f.z_ts_created > ?', $ts);

		foreach($Q->execute(array(), Doctrine::HYDRATE_ARRAY) as $Feed) {
				$return[] = array(
					'time' => $Feed['z_ts_created'],
					'action' => $Feed['action'],
					'operator' => $Feed['Operator']['name'],
					'subject' => $Feed['Subject']['name'],
				);
			}
		$this->Response = new Auslastung_Response_JSON();
		$this->Response->setResult($return, count($return));
	}

	protected function addTeamMember($id)
	{
		$return = array();

		$email = filter_var($this->Request->getInput('email'), FILTER_VALIDATE_EMAIL);
		if (!$email) {
			throw new Auslastung_Exception_Input('E-Mail is invalid.');
		}

		// Find User
		$User = Doctrine_Query::create()
			->select('id, email, name')
			->from('Model_User')
			->where('email = ?', $email)
			->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
		if (!$User) {
			// Add User
			$User = Model_User::createByEmail($email)->toArray();
		}
		$userId = $User['id'];

		// Check if user is assigned
		$XUserOrganization = $this->getAssignment($id, $userId);
		if (!$XUserOrganization) {
			$XUserOrganization = new Model_XUserOrganization();
			$XUserOrganization->organization = $id;
			$XUserOrganization->user = $userId;
			$XUserOrganization->save();
		}
		$this->Response = new Auslastung_Response_JSON();
		$this->Response->setResult($User, 1);
	}

	protected function getAssignment($organization = null, $user = null)
	{
		if ($organization === null) $organization = Auslastung_Session::getInstance()->getOrganizationId();
		if ($user === null) $user = Auslastung_Session::getInstance()->getUserId();
		// Check if user is assigned
		return Doctrine_Query::create()
			->from('Model_XUserOrganization')
			->where('organization = ? AND user = ?', array($organization, $user))
			->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
	}

	protected function removeTeamMember($id)
	{
		$return = array();

		$userId = filter_var($this->Request->getInput('user'), FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
		if (!$userId) {
			throw new Auslastung_Input_Exception('user id is invalid.');
		}

		// Find User
		$User = Doctrine::getTable('Model_User')->find($userId);
		if (!$User) {
			throw new Auslastung_Input_Exception('Unknown user ' . $userId);
		}
		$Orga = Auslastung_Session::getInstance()->getOrganization();

		// Cannot remove owner
		if ((int)$Orga['z_created_by'] === (int)$User['id']) {
			throw new Auslastung_User_Exception('Cannot delete organization owner');
		}

		// Cannot remove myself
		if ((int)$User['id'] === Auslastung_Session::getInstance()->getUserId()) throw new Auslastung_User_Exception('You cannot remove yourself.');



		// Check if user is assigned
		$XUserOrganization = Doctrine_Query::create()
			->from('Model_XUserOrganization')
			->where('organization = ? AND user = ?', array($id, $userId))
			->fetchOne();
		if ($XUserOrganization) $XUserOrganization->delete();
		$this->Response = new Auslastung_Response_JSON();
	}

	protected function getTeamAutocompleter()
	{
		$result = array();
		foreach( Doctrine_Query::create()
			->select('id, name, email')
			->from('Model_User')
			->where('email LIKE :search OR name LIKE :search', array(':search' => '%' . $this->Request->getInput('search') . '%'))
			->execute(array(), Doctrine::HYDRATE_ARRAY) as $User) {
			$result[] = array(
				'id' => $User['id'],
				'name' => $User['name'] . ' <' . $User['email'] . '>',
				'email' => $User['email'],
			);
		}
		$this->Response = new Auslastung_Response_JSON();
		$this->Response->setResult($result, count($result));
	}

	protected function addEntry()
	{
	}

	protected function updateEntry()
	{
		$this->Entry->name = $this->Request->getInput('name');
		$this->Entry->save();
		Auslastung_Session::getInstance()->updateOrganization($this->Entry);
		$this->Response->setResult($this->Entry->toArray(), 1);
	}
}
