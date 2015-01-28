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

abstract class APIConnector
{
	/**
	 * User id of the account connected to the MailPerformance API
	 * @var string
	 */
	static $user_id;

	/**
	 * Customer id of the account connected to the MailPerformance API
	 * @var string
	 */
	static $customer_id;

	/**
	 * Login key of the account connected to the MailPerformance API
	 * @var string
	 */
	static $auto_login_key;

	/**
	 * Flag to clear authentication cookies
	 * @var bool
	 */
	static $clear_cache;

	/**
	 * Last result from API
	 *
	 * JSON document representing the result of a request (may empty, an object or an error)
	 *
	 * @var array
	 */
	var $last_result;

	/**
	 * Last error from API
	 *
	 * String describing last API HTTP error or empty if the last request succeeded
	 *
	 * @var string
	 */
	var $last_error;

	var $settings;
	var $erreur;
	var $rest_client;

	/**
	 * Base path of the API
	 * @var string
	 */
	protected $path;

	protected function __construct()
	{
		// check arguments

		if (func_num_args() == 1)
		{
			$args = func_get_args();
			$this->rest_client = $args[0];
		}
	}

	public static function getError($result, $error)
	{
		if ($error)
			return $result ? $error.'; '.APIConnector::verifNoError($result) : $error;

		return null;
	}

	/**
	 * check the connection with the json array
	 *
	 * @param
	 *        	array from json $result
	 * @return error
	 */
	public static function verifNoError($result)
	{
		if (isset($result['ExceptionMessage'])) // error
			return $result['ExceptionMessage'];
		elseif (isset($result['Message']))
			return $result['Message'];
		elseif (isset($result['errorType']) && isset($result['errorData']))
			return $result['errorData'][0]['value'];

		return '';
	}

	public static function jsonErrorMessage()
	{
		return 'Json is not valid';
	}
}