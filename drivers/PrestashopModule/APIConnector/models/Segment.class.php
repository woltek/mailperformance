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

/**
 * A MailPerformance Segment
 */
class Segment extends EntityAbstract
{
	/**
	 * id
	 * @var integer
	 */
	var $id;

	/**
	 * segment name
	 * @var string
	 */
	var $name;

	/**
	 * segment type (static, dynamic)
	 * @var string
	 */
	var $type;

	/**
	 * description
	 * @var string
	 */
	var $description;

	/**
	 * date format UTC
	 *
	 * @var date
	 */
	var $creation_date;

	/**
	 * date format UTC
	 *
	 * @var date
	 */
	var $expiration_date;

	/**
	 * is a test segment
	 * @var boolean
	 */
	var $for_test;

	/**
	 * target count
	 * @var integer
	 */
	var $target_count;

	/**
	 * search id (for dynamic segment only)
	 * @var integer
	 */
	var $search_id;

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

	protected function parse($values)
	{
		$this->type = $values['type'];
		$this->id = $values['id'];
		$this->name = $values['name'];
		$this->description = $values['description'];
		$this->creation_date = $values['creation'];
		$this->expiration_date = $values['expiration'];
		$this->for_test = $values['isTest'];
		$this->target_count = $values['targetsCount'];

		if (isset($values['searchId']))
			$this->search_id = $values['searchId'];
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

		if (isset($json['id']) && isset($json['type']) && isset($json['name'])
				&& isset($json['creation']) && isset($json['expiration'])
				&& isset($json['isTest']) && isset($json['targetsCount']))
			return true;

		return false;
	}
}

/**
 * enum of segment type
 */
abstract class TypeSegment
{
	const STATIC_SEGMENT = 'static';
	const SPLIT_SEGMENT = 'split';
	const DYNAMIQUE_SEGMENT = 'dynamic';
}