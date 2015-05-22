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

/**
 * A MailPerformance Field
 */
class Field extends EntityAbstract
{
	/**
	 * Field id
	 * @var int
	 */
	var $id;

	/**
	 * Field name
	 * @var string
	 */
	var $name;

	/**
	 * Field type
	 * @var string
	 */
	var $type;

	/**
	 * TODO FieldConstraitModel
	 * @var unknown
	 */
	var $constraint;

	/**
	 * True if field is used as part of the unicity
	 * @var bool
	 */
	var $is_unicity;

	/**
	 * True if field is obligatory
	 * @var bool
	 */
	var $is_obligatory;

	/**
	 * Id of an assoicated value list
	 * @var int
	 */
	var $value_list;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$ctp = func_num_args();
		if ($ctp == 1)
		{
			$args = func_get_args();
			$this->parse($args[0]);
		}
	}

	/**
	 * parse un tableau en attribut
	 *
	 * @param array $record
	 */
	protected function parse($record)
	{
		$this->id = $record['id'];
		$this->name = $record['name'];
		$this->type = $record['type'];
    		$this->is_unicity = boolval($record['isUnicity']);
		$this->is_obligatory = boolval($record['isMandatory']);

		if (isset($record['valueListId']))
			$this->value_list = $record['valueListId'];
		else
			$this->value_list = null;

		if (isset($record['constraint']))
			$this->constraint = array (
					'value' => $record['constraint']['value'],
					'operator' => array (
							'value' => $record['constraint']['operator'],
							'string' => Operators::operatorToString($record['constraint']['operator'])
					)
			);
		else
			$this->constraint = null;
	}

	public function isValueList()
	{
		if (in_array($this->type, array (
				7,
				8,
				9
		)))
			return true;
		return false;
	}

	/**
	 * get the null value null or empty array
	 *
	 * @return null or array()
	 */
	public function getnullValue()
	{
		if ($this->type == TypeField::CHECKBOX)
			return array ();
		return null;
	}

	/**
	 * Format the date to be compreensive by the API
	 *
	 * @param int $timestamp
	 * @return string date
	 */
	public static function getFormatDate($timestamp)
	{
		return 'ISODate("'.date('Y-m-d\TH:i:s\Z', $timestamp).'")';
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

		if (isset($json['id']) && isset($json['name']) && isset($json['type'])
				&& isset($json['isUnicity']) && isset($json['isMandatory']))
			return true;

		return false;
	}

}

/**
 * enum TypeField
 */
abstract class TypeField
{
	const EMAIL = 'email';
	const TEL = 'phone';
	const TEXTAREA = 'textArea';
	const NUMERIC = 'numeric';
	const STRING = 'textField';
	const DATE = 'date';
	const LISTE = 'singleSelectList';
	const CHECKBOX = 'multipleSelectList';
	const RADIOBUTTON = 'singleSelectList';

}

/**
 * enum Operators
 */
abstract class Operators
{
	const NONE = 0;
	const GREATERTHAN = 1;
	const SMALLERTHAN = 2;
	const GREATERTHANOREQUAL = 3;
	const SMALLERTHANOREQUAL = 4;
	const EQUAL = 5;

	public static function operatorToString($op)
	{
		switch ((int)$op)
		{
			case Operators::GREATERTHAN :
				return '>';
			case Operators::SMALLERTHAN :
				return '<';
			case Operators::GREATERTHANOREQUAL :
				return '>=';
			case Operators::SMALLERTHANOREQUAL :
				return '<=';
			case Operators::EQUAL :
				return '==';
		}
		return '';
	}

}