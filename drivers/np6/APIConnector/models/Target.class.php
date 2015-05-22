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
 *  @author	NP6 SAS <contact@np6.com>
 *  @copyright 2014-2014 NP6 SAS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of NP6 SAS
 */

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'Entity.class.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'TargetRedList.class.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'TargetBounce.class.php');

/**
 * A MailPerformance Target
 */
class Target extends EntityAbstract
{
	/**
	 * target id
	 * @var string
	 */
	var $id;

	/**
	 * creation date format UTC
	 * @var string
	 */
	var $creation_date;

	/**
	 * last modification date format UTC
	 * @var string
	 */
	var $last_update_date;

	/**
	 * target values for each field
	 * ("fieldId"=> "field value")
	 * @var array
	 */
	var $fields;

	/**
	* Bounce records
	* @var TargetBounce
	*/
	var $bounce = array();

	/**
	 * Is target in RedList
	 * @var TargetRedList
	 */
	var $red_list;


	/**
	 * constructor
	 *
	 * @param [array $values]
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

	protected function parse($record)
	{
		$this->id = $record['id'];
		$this->creation_date = $record['creationDate'];
		$this->last_update_date = $record['lastUpdateDate'];
		$this->fields = $record['fields'];

		foreach ($record['bounce'] as $bounce_record)
			$this->bounce[] = new TargetBounce($bounce_record);

		if (!empty($record['redList']))
			$this->red_list = new TargetRedList($record['redList']);
	}

	/**
	 * transforms the fields to a array for update
	 * @return array of fields
	 */
	public function getFieldsArray()
	{
		$new_fields = array();
		foreach ($this->fields as $key => $value)
		{
			$new_fields[$key] = $value;

			if (is_string($value))
			{
				$t = strtotime($value);
				if ($t != false)
					$new_fields[$key] = Field::getFormatDate($t);
			}
		}
		return $new_fields;
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

		if (isset($json['id']) && isset($json['creationDate']) && isset($json['lastUpdateDate'])
			&& isset($json['fields']) && isset($json['bounce']) && isset($json['redList'])
			&& TargetRedList::isJsonValid($json['redList']))
		{
			$return_value = true;
			foreach ($json['bounce'] as $bounce_json)
				$return_value &= TargetBounce::isJsonValid($bounce_json);

			return $return_value;
		}

		return false;
	}
}
