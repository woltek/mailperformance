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
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/Target.class.php');

class TargetsConnector extends APIConnector
{

	public function __construct()
	{
		if (func_num_args() == 1)
		{
			call_user_func_array('parent::__construct', func_get_args());

			// def prop
			$this->path = '/targets';
		}
	}

	/**
	 * return a target by an Id
	 *
	 * @param target $id
	 * @return Target
	 */
	public function getTargetById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Target::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Target($this->last_result);
	}

	/**
	 * create a new target
	 *
	 * @param array $values for each field of the new target
	 * @return Target
	 */
	public function createTarget(array $values)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->post($this->path, $values);

		$this->erreur = $this->getError($this->last_result, $this->last_error);

		if (isset($this->erreur))
		{
			return null;
		}
		if (!Target::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Target($this->last_result);
	}

	/**
	 * delete a target by its Id
	 *
	 * @param mixed $id
	 */
	public function deleteTargetById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->delete($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return false;

		return true;
	}

	/**
	 * update a target
	 * @param Target target target with modified values
	 */
	public function updateTarget(Target $target)
	{
		// value argument ave been add too target in np6.php
		return $this->updateTargetFromValues($target->id, $target->values);
	}

	/**
	 * update a target by his values
	 * @param $id The target's id
	 * @param array $values
	 * @return null|Target
	 */
	public function updateTargetFromValues($id, array $values)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->put($this->path.'/'.$id, $values);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Target::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Target($this->last_result);
	}
}
