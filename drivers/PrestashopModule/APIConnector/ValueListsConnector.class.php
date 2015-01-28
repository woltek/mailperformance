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
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/ValueList.class.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/Field.class.php');

class ValueListsConnector extends APIConnector
{
	private $value_lists;

	public function __construct()
	{
		if (func_num_args() == 1)
		{
			call_user_func_array('parent::__construct', func_get_args());

			// def prop
			$this->path = '/valuelists';
			$this->value_lists = array ();
		}
	}

	/**
	 * get all value list
	 *
	 * @return Array of ValueList
	 */
	public function getValueLists()
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result[0]) && !ValueList::isJsonValid($this->last_result[0]))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		$this->value_lists = array ();
		foreach ($this->last_result as $value_list)
			$this->value_lists[] = new ValueList($value_list);

		return $this->value_lists;
	}

	/**
	 * get a value list his Id
	 *
	 * @param string $id
	 * @return ValueList
	 */
	public function getValueListById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result) && !ValueList::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new ValueList($this->last_result);
	}

	/**
	 * get a value List by a field object
	 *
	 * @param Field $field
	 * @return \APIConnector\ValueList|null
	 */
	public function getValueListByField(Field $field)
	{
		if ($field->value_list != null)
			return $this->getValueListById($field->value_list);

		$this->erreur = 'ValueList is null';
		return null;
	}

	/**
	 * Delete a value list by his id
	 *
	 * @param int $id
	 * @return bool
	 */
	public function deleteValueListById($id)
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
	 * Delete a value from a value list
	 *
	 * @param int $value_list_id
	 *        	the value list id
	 * @param int $value_index
	 *        	the index of the value
	 * @return the updated ValueList or null if error
	 */
	public function deleteAValueFromValueList($value_list_id, $value_index)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->delete($this->path.'/'.$value_list_id.'/values/'.$value_index);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result) && !ValueList::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new ValueList($this->last_result);
	}

	/**
	 * Modify a value from a valueList
	 *
	 * @param mixed $value_list_id
	 * @param mixed $value_index
	 * @param string $new_value
	 * @return Updated ValueList or null if error
	 */
	public function updateAValueFromValueList($value_list_id, $value_index, $new_value)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->put($this->path.'/'.$value_list_id.'/values/'.$value_index, $new_value);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result) && !ValueList::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new ValueList($this->last_result);
	}

	/**
	 * Summary of addAValueInAValueList
	 *
	 * @param int $value_list_id
	 * @param string $new_value
	 * @return the updated ValueList or null if error
	 */
	public function addAValueInAValueList($value_list_id, $new_value)
	{
		$content = array (
				'index' => 0,
				'value' => $new_value.''
		);

		list($this->last_result, $this->last_error) = $this->rest_client->post($this->path.'/'.$value_list_id.'/values', $content);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result) && !ValueList::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new ValueList($this->last_result);
	}
}