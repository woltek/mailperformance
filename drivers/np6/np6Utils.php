<?php

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'APIConnector'.DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');

class np6Utils 
{

	// function for optimize add in smarty array
	public static function SetSmartArrayFieldValue($smarty_array, $fieldName, $value)
	{
		//return the new smarty_array with the new field set; 
		if($value)
		{
			$smarty_array[$fieldName] = $value;
			return $smarty_array;
		}
		return $smarty_array;
	}
	
	/**
	 * delete all configuration file from
	 * @return boolean
	 */
	public static function deleteConfigurationFile()
	{
		$result = true;
		$result = Configuration::deleteByName(Constants::SETTINGS_STR) && $result;
		$result = Configuration::deleteByName(Constants::FORM_STTGS_PAGE) && $result;
		$result = Configuration::deleteByName(Constants::IMPORT_STTGS) && $result;
		$result = Configuration::deleteByName(Constants::FORM_STTGS) && $result;
		$result = Configuration::deleteByName(Constants::EVENT_STTGS) && $result;
		$result = Configuration::deleteByName(Constants::EVENT_CART_STTGS) && $result;
		return $result;
	}

	/**
	 * Get an array with infos for the view
	 *
	 * @return array of info
	 */
	public static function createASmartyArray($param)
	{
		//return the new smarty_array 

		$languages = Language::getLanguages(false);

		return array (

				'admin_tpl_path' => $param['admin_tpl_path'],
				'hooks_tpl_path' => $param['hooks_tpl_path'],
				'info' => array (
						'module' => $param['module'],
						'name' => Configuration::get('PS_SHOP_NAME'),
						'domain' => Configuration::get('PS_SHOP_DOMAIN'),
						'email' => Configuration::get('PS_SHOP_EMAIL'),
						'version' => $param['version'],
						'psVersion' => _PS_VERSION_,
						'php' => phpversion(),
						'mysql' => Db::getInstance()->getVersion(),
						'theme' => _THEME_NAME_,
						'today' => date('Y-m-d'),
						'module' => $param['module'],
						'context' => (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 0) ? 1 :
						($param['context']->shop->getTotalShops() != 1) ? $param['context']->shop->getContext() : 1
				),
				'form_action' => 'index.php?tab=AdminModules&configure='.$param['module'].'&token='
				.Tools::getAdminTokenLite('AdminModules').'&tab_module='.$param['tab'].'&module_name='.$param['module'],
				'hooks' => $param['hooks'],
				'action_hooks' => $param['action_hooks'],
				'cart_hooks' => $param['cart_hooks'],
				'isConnected' => $param['isConnected'],
				'DBfield' => $param['DBfield'],
				'DateFormat' => array (
						'JJ/MM/AAAA' => 'd/m/Y',
						'MM/JJ/AAAA' => 'm/d/Y',
						'JJ/MM/AA' => 'd/m/y',
						'YYYY/MM/DD' => 'Y/m/d',
						'JJ-MM-AAAA' => 'd-m-Y',
						'JJ-MM-AA' => 'd-m-y',
						'AAAA-MM-JJ' => 'Y-m-d'
				),
				'languages' => $languages,
				'link' => $param['context']->link,
				'default_lang' => $param['context']->language->id,
				'flags' => $param['flags']
		);
	}

	/**
	 * Check if API's connector send an error
	 */
	public static function checkApiError($param)
	{
		// if error return array of all error else null

		if ($param['mperf']->forms->erreur != '' || $param['mperf']->fields->erreur != ''
				|| $param['mperf']->value_lists->erreur != '' || $param['mperf']->contacts->erreur != ''
				|| $param['mperf']->segments->erreur != '')
		{
			return array (
					'text' => $param['apiError'].'<br>'.$param['mperf']->segments->erreur
					.($param['mperf']->segments->erreur == '' ? '' : '<-segment<br>').$param['mperf']->contacts->erreur
					.($param['mperf']->contacts->erreur == '' ? '' : '<-contact<br>').$param['mperf']->value_lists->erreur
					.($param['mperf']->value_lists->erreur == '' ? '' : '<- valueList<br>').$param['mperf']->fields->erreur
					.($param['mperf']->fields->erreur == '' ? '' : '<-fields<br>').$param['mperf']->forms->erreur
					.($param['mperf']->forms->erreur == '' ? '' : '<-form'),
					'type' => 'warning'
			);
		}
		return null;
	}


	/**
	 * Get an array with the database fields
	 *
	 * @return array
	 */
	public static function getDBfield($param)
	{
		// return a array with DB information

		return array (
				array (
						'dbName' => 'id_customer',
						'type' => array (
								TypeField::NUMERIC
						),
						'name' => 'id'
				),
				array (
						'dbName' => 'id_gender',
						'distinctValues' => array (
								'1' => $param['Mr.'],
								'2' => $param['Ms.'],
						),
						'type' => array (
								TypeField::RADIOBUTTON,
								TypeField::CHECKBOX,
								TypeField::LISTE
						),
						'name' => $param['Title']
				),
				array (
						'dbName' => 'firstname',
						'type' => array (
								TypeField::STRING, TypeField::TEXTAREA
						),
						'name' => $param['First name']
				),
				array (
						'dbName' => 'lastname',
						'type' => array (
								TypeField::STRING, TypeField::TEXTAREA
						),
						'name' => $param['Last name']
				),
				array (
						'dbName' => 'email',
						'type' => array (
								TypeField::EMAIL
						),
						'name' => $param['E-mail']
				),
				array (
						'dbName' => 'birthday',
						'type' => array (
								TypeField::DATE
						),
						'name' => $param['Birthdate']
				),
				array (
						'dbName' => 'newsletter_date_add',
						'type' => array (
								TypeField::DATE
						),
						'name' => $param['Subscribe to the newsletter date']
				),
				array (
						'dbName' => 'optin',
						'distinctValues' => array (
								'0' => $param['no'],
								'1' => $param['yes']
						),
						'type' => array (
								TypeField::RADIOBUTTON,
								TypeField::CHECKBOX,
								TypeField::LISTE
						),
						'name' => $param['Third party offers']
				)
		);
	}


	//link a field in import_bind
	public static function bindField($field_name, $fiellink_name, $dbfield ,& $import_bind, & $oblig_fields)
	{
		if (Tools::isSubmit($field_name.$dbfield['dbName']))
			{
				$api_id = Tools::getValue($field_name.$dbfield['dbName']);

				$import_bind['fields'][$dbfield['dbName']] = array (
						'apiFieldId' => $api_id
				);

				if ($api_id != 0 && isset($dbfield['distinctValues']))
				{
					$import_bind['fields'][$dbfield['dbName']]['binding'] = array ();
					// bindings for value list
					foreach ($dbfield['distinctValues'] as $localvalue => $value)
					{
						if ($value != null)
						{
							$import_bind['fields'][$dbfield['dbName']]['binding'][$localvalue] = Tools::getValue($fiellink_name.$dbfield['dbName'].$localvalue);
						}
					}		
				}
				// remove the just binf value from the list oblig_fields
				if (isset($oblig_fields[$api_id]))
				{
					unset($oblig_fields[$api_id]);
				}		
				if (Tools::isSubmit('dateFormat'.$dbfield['dbName']))
				{
					$import_bind['fields'][$dbfield['dbName']]['dateFormat'] = Tools::getValue('dateFormat'.$dbfield['dbName']);	
				}
			}
	}

}


