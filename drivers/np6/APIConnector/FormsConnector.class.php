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
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/Form.class.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/FormDetails.class.php');

class FormsConnector extends APIConnector
{
	private $list_forms;

	public function __construct()
	{
		if (func_num_args() == 1)
		{
			call_user_func_array('parent::__construct', func_get_args());

			// def prop
			$this->path = '/forms';
			$this->list_forms = array ();
		}
	}

	/**
	 * Retrieve a array of getForms
	 *
	 * @return array of \Form
	 */
	public function getForms()
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result['records'][0]) && !Form::isJsonValid($this->last_result['records'][0]))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		// parse result in array of object
		foreach ($this->last_result['records'] as $record)
			$this->list_forms[] = new \Form($record);

		return $this->list_forms;
	}

	/**
	 * Retrieve values by Form type
	 *
	 * @param array $type
	 * @param bool $valid_only
	 *        	= true get only the form mark as valid
	 * @return array of \Form
	 */
	public function getListFormByTypes(array $type, $valid_only = true)
	{

		if (count($this->list_forms) <= 0)
			if ($this->getForms() == null)
				return null;

		$return_list = array ();
		foreach ($this->list_forms as $form)
			if (in_array($form->type, $type) && (!$valid_only || $form->state == FORM_STATE_VALID))
				$return_list[] = $form;

		return $return_list;
	}

	/**
	 * Retrieve a Detail Form by ID
	 *
	 * @param string $id
	 * @return \FormDetails
	 */
	public function getDetailFormById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!FormDetails::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new FormDetails($this->last_result, $this->getFormById($id));
	}

	/**
	 * Retrieve a Form by ID
	 *
	 * @param string $id
	 * @return \Form
	 */
	public function getFormById($id)
	{
		if (count($this->list_forms) <= 0 && $this->getForms() == null)
			return null;

		foreach ($this->list_forms as $form)
			if ($form->id == $id)
				return $form;

		return null;
	}
}