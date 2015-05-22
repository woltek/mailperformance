<?php
/**
 * 2014-2014 NP6 SAS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    NP6 SAS <contact@np6.com>
 *  @copyright 2014-2014 NP6 SAS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of NP6 SAS
 */

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/Contact.class.php');


class ContactsConnector extends APIConnector
{
	private $list_contacts;

	public function __construct()
	{
		if (func_num_args() == 1)
		{
			call_user_func_array('parent::__construct', func_get_args());

			// def prop
			$this->path = '/contacts';
			$this->list_contacts = array ();
		}
	}

	/**
	 * get all contact
	 *
	 * @return array of contact
	 */
	public function getContacts()
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result[0]) && !ContactMP::isJsonValid($this->last_result[0]))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		$this->list_contacts = array ();
		foreach ($this->last_result as $contact)
			$this->list_contacts[] = new ContactMP($contact);

		return $this->list_contacts;
	}

	/**
	 * Get contact by Id
	 *
	 * @param string $id
	 * @return ContactDetails
	 */
	public function getContactById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!ContactMP::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}
		return new ContactMP($this->last_result);
	}

	/**
	 * delete contact by id
	 *
	 * @param string $id
	 * @return bool
	 */
	public function deleteContactById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->delete($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return false;

		// if success
		if (isset($this->last_result['success']) && $this->last_result['success'])
			return true;

		return false;
	}

	/**
	 * edit detail contact
	 *
	 * @param ContactDetails $contact
	 * @return mixed
	 */
	public function editContact(ContactMP $contact)
	{
		$modtab = array (
				'id' => $contact->id,
				'firstName' => $contact->last_name,
				'lastName' => $contact->last_name,
				'email' => $contact->email,
				'politness' => $contact->politness
		);
		list($this->last_result, $this->last_error) = $this->rest_client->put($this->path.'/'.$contact->id, $modtab);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!ContactMP::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}
		return new ContactMP($this->last_result);
	}

}