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

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'Entity.class.php');
define('FORM_STATE_VALID', 50);

/**
 * A Mailperformance Form
 */
class Form extends EntityAbstract
{
	/**
	 * Form id
	 * @var string
	 */
	var $id;

	/**
	 * Form name
	 * @var string
	 */
	var $name;

	/**
	 * Form type
	 * @var int
	 */
	var $type;

	/**
	 * Form category id
	 * @var int
	 */
	var $category;

	/**
	 * Form creation date
	 * @var string
	 */
	var $creation_date;

	/**
	 * Form worklflow state
	 * @var int
	 */
	var $state;

	/**
	 * Form parent id
	 * @var int
	 */
	var $parent_id;

	/**
	 * Form permission
	 * @var int
	 */
	var $permission;

	/**
	 * Form contextual actions id
	 * @var int
	 */
	var $context_id;

	/**
	 * constructor
	 * Form($tableauAparser)
	 * Form($form, bool $fromForm)
	 */
	public function __construct()
	{
		$ctp = func_num_args();
		$args = func_get_args();
		switch ($ctp)
		{
			case 1 :
				$this->parse($args[0]);
				break;
			case 2 :
				$this->fromForm($args[0]);
				break;
			default :
				break;
		}
	}

	/**
	 * Parse the array to fill object values
	 *
	 * @param array $record
	 */
	protected function parse($record)
	{
		$this->id = $record[0];
		$this->name = $record[1];
		$this->type = $record[2];
		$this->category = $record[3];
		$this->creation_date = $record[4];
		$this->state = $record[5];
		$this->parent_id = $record[6];
		$this->permission = $record[7];
		$this->context_id = $record[8];
	}

	/**
	 * copy the old object in this
	 *
	 * @param Form $form
	 */
	private function fromForm(Form $form)
	{
		$this->id = $form->id;
		$this->name = $form->name;
		$this->type = $form->type;
		$this->category = $form->category;
		$this->creation_date = $form->creation_date;
		$this->state = $form->state;
		$this->parent_id = $form->parent_id;
		$this->permission = $form->permission;
		$this->context_id = $form->context_id;
	}

	/**
	 * check if the json array is valid
	 * @param array $json
	 * @return boolean
	 */
	public static function isJsonValid($json)
	{
		if (empty($json))
			return true;

		if (count($json) >= 9)
			return true;

		return false;
	}
}
