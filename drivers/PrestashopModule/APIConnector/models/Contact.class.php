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
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'ContactIdentity.class.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'ContactStatus.class.php');

/**
 * A MailPerformance Contact (must not conflict with Prestashop Contact)
 */
class ContactMP extends EntityAbstract
{
	/**
	 * Contact Id
	 * @var string
	 */
	var $id;

	/**
	 * Login
	 * @var string
	 */
	var $login;

	/**
	 * Email
	 * @var string
	 */
	var $email;

	/**
	 * Contact Identity
	 * @var ContactIdentity
	 */
	var $identity;

	/**
	 * Expiration date
	 * @var unknown
	 */
	var $expire;

	/**
	 * Contact status
	 * @var unknown
	 */
	var $status;

	/**
	 * Creation date
	 * @var unknown
	 */
	var $creation_date;

	/**
	 * Creator contact Id
	 * @var string
	 */
	var $creator;

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
		$this->login = $record['login'];
		$this->email = $record['email'];
		$this->identity = new ContactIdentity($record['identity']);
		$this->status = new ContactStatus($record['status']);

		if (isset($record['expire']) && $record['expire']['isdefined'])
			$this->expire = $record['expire']['value'];

		if (isset($record['creationDate']))
			$this->creation_date = $record['creationDate'];

		if (isset($record['creator']))
			$this->creator = $record['creator'];
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

		if (isset($json['id']) && isset($json['login']) && isset($json['email'])
				&& isset($json['identity']) && ContactIdentity::isJsonValid($json['identity'])
				&& isset($json['status']) && ContactStatus::isJsonValid($json['status']))
			return true;

		return false;
	}
}
