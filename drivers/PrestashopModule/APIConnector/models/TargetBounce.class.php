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
 * A MailPerformance Bounce information for a target
 */
class TargetBounce extends EntityAbstract
{
	/**
	 * The id of field of the bounce
	 * @var int
	 */
	var $field;

	/**
	 * The type of the bounce
	 * @var TargetBounceState
	 */
	var $type;

	/**
	 * Date when the bounce was reported, format UTC
	 * @var string
	 */
	var $timestamp;

	/**
	 * Reason for the bounce
	 * @var string
	 */
	var $reason;

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

	protected function parse($record)
	{
		$this->field = $record['field'];
		$this->type = $record['type'];
		$this->timestamp = $record['timestamp'];
		$this->reason = $record['reason'];
	}


	/**
	 * check if the json array is valid
	 * @param array $json
	 * @return boolean
	 */
	public static function isJsonValid($json)
	{
		if (isset($json['field']) && isset($json['type']) && isset($json['timestamp']) && isset($json['reason']))
			return true;

		return false;
	}
}

abstract class TargetBounceState
{
	const VALIDATED = 3;

	const SOFT = 4;

	const HARDDOMAIN = 51;

	const HARDUSER = 52;

	const HARDOTHER = 53;

	const HARDRULES = 54;

	const REPORTASSPAM = 6;
}