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
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/Field.class.php');

class FieldsConnector extends APIConnector
{
	private $list_fields;

	public function __construct()
	{
		if (func_num_args() == 1)
		{
			call_user_func_array('parent::__construct', func_get_args());

			// def prop
			$this->path = '/fields';
			$this->list_fields = array ();
		}
	}

	/**
	 * Retrieve all fields
	 *
	 * @return array of \Field
	 */
	public function getListFields()
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result[0]) && !Field::isJsonValid($this->last_result[0]))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		$this->list_fields = array(); // clear the tab

		foreach ($this->last_result as $record)
			$this->list_fields[] = new Field($record);

		return $this->list_fields;
	}

	/**
	 * Retrieve all fields
	 *
	 * @return array of \Field
	 */
	public function getIndexedFields()
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result[0]) && !Field::isJsonValid($this->last_result[0]))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		$indexedlist = array ();
		foreach ($this->last_result as $record)
		{
			$field = new Field($record);
			$indexedlist[$field->id] = $field;
		}

		return $indexedlist;
	}

	/**
	 * Retrieve all field by type
	 *
	 * @param int $type
	 * @return array of \Field
	 */
	public function getListFieldsByType(array $type)
	{
		// if list of field is empty
		if (count($this->list_fields) <= 0)
			if ($this->getListFields() == null)
				return null;

		$returnlist = array ();
		foreach ($this->list_fields as $field)
			if (in_array($field->type, $type))
				$returnlist[] = $field;

		return $returnlist;
	}

	/**
	 * Retrieve the field
	 *
	 * @param int $id
	 * @return \Field
	 */
	public function getFieldById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Field::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Field($this->last_result);
	}
}