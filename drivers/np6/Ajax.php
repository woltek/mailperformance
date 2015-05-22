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

include_once (dirname(__FILE__).'/../../config/config.inc.php');
include_once (dirname(__FILE__).'/../../init.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'APIConnector'.DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'PrestashopClasses'.DIRECTORY_SEPARATOR.'Constants.class.php');
class Ajax
{
	/**
	 * return values from a value list
	 */
	public function getValueListFromFieldId()
	{
		if (Tools::isSubmit('id'))
		{
			$settings = unserialize(Configuration::get(Constants::SETTINGS_STR));
		    if($settings && isset($settings['alkey'])) {
				$mperf = new MailPerformance();
				$mperf->auto_login_key = $settings['alkey'];
				$mperf->authFromAlk();

				$field_detail = $mperf->fields;
				$result = $mperf->fields->getFieldById(Tools::getValue('id'));
				if (!$result)
					return $mperf->fields->erreur;

				$result = $mperf->value_lists->getValueListByField($result);

				return Tools::jsonEncode($result);
			}
		}
	}

	/**
	 * check if the new auto login key is valid
	 * and if it's the same customer than the key before
	 */
	public function getCheckAutoLoginKey()
	{
		$result = array('success'=>false,'changeCustomer'=>false,'alkeyValid'=>false);
		if (Tools::isSubmit('id')) {

			$result['success'] = true; // always true, funny
			$settings = unserialize(Configuration::get(Constants::SETTINGS_STR));
			$new_alkey = Tools::getValue('id');
			$mperf = new MailPerformance();

			// if key is already set, authenticate and find the old customer
			if ($settings && isset($settings['alkey'])) {
			    $mperf->auto_login_key = $settings['alkey'];

				if($mperf->authFromAlk())
				{
					$old_customer = $mperf->customer_id;
					$old_alkey = $settings['alkey'];

					// if the keys are not the same re-authenticate and compare the customer ids
					if ($old_alkey != $new_alkey) {
						$mperf->auto_login_key=$new_alkey;
						if ($mperf->authFromAlk()) {
							$result['alkeyValid'] = true;
							if ($mperf->customer_id != $old_customer) {
								$result['changeCustomer'] = true;
							}
						}
					}
					else {
					    // if the keys are the same we autheticated so key is valid
						$result['alkeyValid'] = true;
					}
				}
			}
			else {
			    // on first connection we have only the new key
			    $mperf->auto_login_key = $new_alkey;

			    if($mperf->authFromAlk()) {
					$result['alkeyValid'] = true;
			    }
			}
		}
		return Tools::jsonEncode($result);
	}
}

/**
 * executed on ajax request
 */
if (Tools::isSubmit('ajax') && Tools::isSubmit('methode'))
{
	$ajax = new Ajax();
	switch (Tools::getValue('methode'))
	{
		case 'getValueListFromFieldId' :
			echo $ajax->getValueListFromFieldId();
			break;
		case 'getCheckAutoLoginKey' :
			echo $ajax->getCheckAutoLoginKey();
			break;
		default :
			echo Tools::jsonEncode(array (
					'error' => 'method not exist'
			));
			break;
	}
}