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
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'Form.class.php');

class FormDetails extends Form
{
	/**
	 * URL of the form (TODO change name of variable)
	 * @var string
	 */
	var $preview_location;

	/**
	 * constructor
	 *
	 * @param $details_tab request
	 *        	result as array
	 * @param Form $form
	 *        	can be null
	 */
	public function __construct($details_tab, $form)
	{
		if (!empty($form))
			parent::__construct($form, true);

		$this->parse($details_tab);
	}

	/**
	 * parse un tableau en attribut
	 *
	 * @param array $record
	 */
	protected function parse($details_tab)
	{
		$this->preview_location = $details_tab['informations']['previewLocation'];
	}

	/**
	 * check if the json array is valid
	 * @param array $json
	 * @return boolean
	 */
	public static function isJsonValid($json)
	{
		if (isset($json['informations']['previewLocation']))
			return true;
		return false;
	}
}
