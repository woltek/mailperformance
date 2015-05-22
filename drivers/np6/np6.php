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


if (!defined('_PS_VERSION_'))
	exit();

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'APIConnector'.DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'PrestashopClasses'.DIRECTORY_SEPARATOR.'PrestashopClassesIncludes.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'np6Utils.php');


/**
 * np6 module class
 *
 * @author np6
 *
 */
class Np6 extends Module
{
	var $mperf;
	var $is_connected;
	var $cms_page_list;
	var $cms_add;
	var $message;
	var $data;
	var $smarty_array;
	protected $db_name_target_error;
	protected $db_name_mp_link;
	protected $admin_tpl_path;
	protected $hooks_tpl_path;
	protected $dashboard_tpl_path;
	protected $tab_index_to_open;
	protected $action_hooks;
	protected $abandonned_cart_hooks;


	public function __construct()
	{
		// prop used by prestashop
		$this->name = 'np6';
		$this->tab = 'emailing';
		$this->version = 0.1;
		$this->author = 'NP6';
		$this->need_instance = 0;
		$this->db_name_target_error = $this->name.'TargetError';
		$this->db_name_mp_link = $this->name.'MPuserLink';

		parent::__construct();

		$this->displayName = $this->l('MailPerformance');
		$this->description = $this->l('MailPerformance integration module');
		$this->tab_index_to_open = 0;
		$this->admin_tpl_path = dirname(__FILE__).'/views/templates/admin/';
		$this->hooks_tpl_path = dirname(__FILE__).'/views/templates/hooks/';
		$this->dashboard_tpl_path = dirname(__FILE__).'/views/templates/dashboard/';

		// API connector creation
		$this->mperf = new MailPerformance();

		$this->cms_page_list = new CmsList();
		$this->cms_add = new AddCmsPage();

		$this->hooks = array (
				array (
						'text' => $this->l('Top'),
						'hook' => 'Top'
				),
				array (
						'text' => $this->l('Left Column'),
						'hook' => 'LeftColumn'
				),
				array (
						'text' => $this->l('Right Column'),
						'hook' => 'RightColumn'
				),
				array (
						'text' => $this->l('Footer'),
						'hook' => 'Footer'
				),
				array (
						'text' => $this->l('Home'),
						'hook' => 'Home'
				),
				array (
						'text' => $this->l('Product Left Column'),
						'hook' => 'LeftColumnProduct'
				),
				array (
						'text' => $this->l('Product Right Column'),
						'hook' => 'RightColumnProduct'
				),
				array (
						'text' => $this->l('Product footer'),
						'hook' => 'FooterProduct'
				)
		);

		//action hook for segment changement on prestashop event
		//fields => array(nom => array(trad, type))
		$this->action_hooks = array(
				'actionCartSave' => array('help' => $this->l('Called after a cart creation or update.'),
										'fields' => array(
												'modifDate' => array(
														$this->l('cart modification date')
														, TypeField::DATE)
												, 'nbArticle' => array(
														$this->l('numbers of articles in cart')
														, TypeField::NUMERIC)
												, 'cartPrice' => array(
													$this->l('cart price')
													, TypeField::STRING))),
				'actionValidateOrder' => array('help' =>  $this->l('Called during the order validation process')
											, 'fields' => array(
												'modifDate' => array(
														$this->l('validation date')
														, TypeField::DATE)
												, 'moyenPayement' => array(
														$this->l('payment method')
														, TypeField::STRING)
												, 'totalPaid' => array(
														$this->l('total paid')
														, TypeField::STRING))),
				'actionPaymentConfirmation' => array('help' =>  $this->l('Called when an order\'s status becomes "Payment accepted"')
											, 'fields' => array(
												'payementDate' => array(
														$this->l('payment date')
														, TypeField::DATE))),
				/*'actionOrderStatusPostUpdate' => $this->l('Called when an order\'s status is changed'), */
				'actionProductCancel' => array('help' =>  $this->l('Called when an item is deleted from an order, right after the deletion')
										, 'fields' => array(
												'modifDate' => array(
														$this->l('cancel date')
														, TypeField::DATE))),
				'actionOrderReturn' => array('help' => $this->l('Called when the customer request to send his merchandise back to the store')
										, 'fields' => array(
											'modifDate' => array(
												$this->l('date modif')
												,TypeField::DATE)
											, 'reason' => array(
												$this->l('reason')
												, TypeField::TEXTAREA)))
								);
		$this->abandonned_cart_hooks = array(
			'lastModifDate' => array('text' => $this->l('Cart last modification date')),
			'confirmationDate' => array('text' => $this->l('Cart confirmation date'))
		);
	}

	/**
	 * function execute when the module is installing
	 *
	 * @return bool
	 */
	public function install()
	{
		// creation de la table de log d'erreurs d'ajout de target
		Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->db_name_target_error.'` (
				`id` int(11) NOT null AUTO_INCREMENT,
				`customer_Id` int(11) NOT null,
				`errorText` text COLLATE utf8mb4_bin NOT null,
				`errorTimestamp` int(11) NOT null,
				PRIMARY KEY (`id`)
				) ENGINE= InnoDB DEFAULT CHARSET= utf8mb4 COLLATE= utf8mb4_bin AUTO_INCREMENT= 1 ;');

		Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->db_name_mp_link.'` (
				`idMP` char(8) NOT null,
				`idPresta` int(11) NOT null,
				PRIMARY KEY (`idPresta`)
				) ENGINE= InnoDB DEFAULT CHARSET= utf8mb4 COLLATE= utf8mb4_bin;');
				// enregistrement des hooks pour l'affichage des formulaire et autres
		return parent::install() && function_exists('curl_version')
		&& $this->registerHook('displayBackOfficeHeader')
		&& $this->registerHook('displayHome') && $this->registerHook('actionCustomerAccountAdd')
		&& $this->registerHook('createAccount') && $this->registerHook('displayHeader')
		&& $this->registerHook('actionCartSave') && $this->registerHook('actionOrderReturn')
		&& $this->registerHook('actionValidateOrder') && $this->registerHook('actionPaymentConfirmation')
		&& $this->registerHook('actionProductCancel') && $this->registerHook('actionCarrierUpdate')
		&& $this->registerHook('actionCustomerAccountUpdate');
	}

	/**
	 * function execute when the module is uninstalling
	 *
	 * @return bool
	 */
	public function uninstall()
	{
		if (!parent::uninstall())
		{
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->db_name_target_error.'`');
			//Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->db_name_mp_link.'`');
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'hook` WHERE name= "actionCustomerUpdate"');
		}

		return parent::uninstall() && np6Utils::deleteConfigurationFile();
	}


	/**
	 * administration page
	 */
	public function getContent()
	{
		$this->smarty_array = np6Utils::createASmartyArray($this->sendThisInformation());

		if (function_exists('curl_version'))
		{
			$settings = unserialize(Configuration::get(Constants::SETTINGS_STR));

			// connection to the API when we modified the alKey
			if (Tools::isSubmit('submitMailPerfAuth'))
			{
				$this->verifFormSubmit();
			}
			elseif (isset($settings['alkey']) && !$this->apiConnexion($settings['alkey']))
			{
				// connection fail
				$this->messageConnexionError();
			}				
			else
			{
				// conection ok
				// check if forms are submit
				$this->verifFormSubmit();
			}
				
			if ($this->is_connected) // getting data from the API only when we are connected
			{
				$this->getInformationApi();
		 	}
			else
				$this->messageConnexionError();
		}
		else
		{
			$this->message = $this->setMessage('Install cURL to use the module.','warning');
		}

        //need a default value
        if(!isset($this->smarty_array['formListTypeAll']))
        {
            $this->smarty_array['formListTypeAll'] = [];
        }

		$this->smarty_array['message'] = ($this->message['text']) ? $this->message : false;

		// assign the samrty array to the view
		$this->smarty->assign('np6', $this->smarty_array);

		// display the frontend
		return $this->display(__FILE__, 'views/templates/admin/mailPerf.tpl');
	}

	private function getInformationApi()
	{
		// put information from api to smarty_array

		// get new settings values
		$settings = unserialize(Configuration::get(Constants::SETTINGS_STR));
		// smarty for values in the view
		$this->smarty_array = np6Utils::SetSmartArrayFieldValue($this->smarty_array,'userSettings',$settings);

		$this->smarty_array['isConnected'] = true;

        $this->smarty_array['formListTypeAll'] = $this->mperf->forms->getListFormByTypes(array (
            '1',
            '2',
            '3',
            '7'
        ));

		$this->data = unserialize(Configuration::get(Constants::FORM_STTGS));
		$this->smarty_array = np6Utils::SetSmartArrayFieldValue($this->smarty_array,'data',$this->data);
			
		$syncparam = unserialize(Configuration::get(Constants::IMPORT_STTGS));
		$this->smarty_array = np6Utils::SetSmartArrayFieldValue($this->smarty_array,'importSet', $syncparam);

		$eventparam = $this->getEventSettings();
		$this->smarty_array = np6Utils::SetSmartArrayFieldValue($this->smarty_array,'eventSet', $eventparam);
	
		$event_cart_param = unserialize(Configuration::get(Constants::EVENT_CART_STTGS));
		$this->smarty_array = np6Utils::SetSmartArrayFieldValue($this->smarty_array,'eventCart', $event_cart_param);

		$this->smarty_array['tabIndex'] = $this->tab_index_to_open;
		$this->smarty_array['formListType1'] = $this->mperf->forms->getListFormByTypes(array (
				'1'
		));
		
	 	$this->smarty_array['APIFields'] = $this->mperf->fields->getListFields();
	 	$this->smarty_array['userSettings']['contact'] = $this->mperf->contacts->getContactById($this->mperf->user_id);

	 	$this->smarty_array['listCmsPage'] = $this->cms_page_list->getCmsList();
	 	$this->smarty_array['segmentsList'] = $this->mperf->segments->getSegmentByTypes(TypeSegment::STATIC_SEGMENT);
	 	
	 	$tempErrorMessage = np6Utils::checkApiError($this->sendThisInformation());
	 	if($tempErrorMessage != null)
	 	{
	 		$this->message = $tempErrorMessage;
	 	}	
	}

	
	/**
	 * connection to the API
	 *
	 * @param string $apikey
	 * @return bool is authenticate
	 */
	public function apiConnexion($apikey, $clear_cache = false)
	{
		// authentification
		$this->mperf->auto_login_key = $apikey;

		$is_success = $this->mperf->authFromAlk();

		$this->mperf->forms->getForms();
		if ($is_success || empty($this->mperf->forms->erreur))
		{
			$this->is_connected = true;
			return true;
		}
		$this->is_connected = false;
		return false;
	}


	/**
	 * Save settings into Prestashop Configuration
	 */
	private function configureAuthNp6()
	{
		$settings = array ();

		//check if the customer change
		if (Tools::isSubmit('clearAllValues') && Tools::getValue('clearAllValues') == 'true')
		{
			$this->clearAllData();
		}
			
		// Get user key
		if (!Tools::isSubmit('alkey') || !Tools::getValue('alkey') || trim(Tools::getValue('alkey')) == '')
		{
			$this->message = $this->setMessage('empty auto login key','error');
		}

		// saves the settings if key different than empty
		if (!empty(Tools::getValue('alkey')) && trim(Tools::getValue('alkey')) != '')
		{
			$settings = array (
					'alkey' => Tools::getValue('alkey')
			);
			Configuration::updateValue(Constants::SETTINGS_STR, serialize($settings));

			if (!$this->apiConnexion($settings['alkey'], true))
			{
				// if error
				$this->messageConnexionError();
			}				
			else
			{
				// Saved message
 				$this->message = $this->setMessage('Saved!','valid');
			}
		}
	}

	/**
	 * delete all data from the former user
	 */
	private function  clearAllData()
	{
		//clear configuration 
		np6Utils::deleteConfigurationFile();
		$this->cms_page_list->deleteAllCmsList();
		$sql = 'DELETE FROM '._DB_PREFIX_.$this->db_name_target_error.' ';
		Db::getInstance()->Execute($sql);
		$sql = 'DELETE FROM '._DB_PREFIX_.$this->db_name_mp_link.' ';
		Db::getInstance()->Execute($sql);
	}

	/**
	 * Add a form on a static CMS page
	 */
	private function configureFormPage()
	{
		$form_result = array ();

		// check form infos
		if (!Tools::isSubmit('CMStitre0') || empty(Tools::getValue('CMStitre0')))
		{
			$this->message = $this->setMessage('Empty title!','error');
			return;
		}
		if (!Tools::isSubmit('CMSform0') || empty(Tools::getValue('CMSform0')))
		{
			$this->message = $this->setMessage('No form selected!','error');
			return;
		}

		// saves settings
		$form_result = array (
				'CMStitre' => Tools::getValue('CMStitre0'),
				'CMSform' => Tools::getValue('CMSform0')
		);

		// get the form link
		$detail_from = $this->mperf->forms->getDetailFormById($form_result['CMSform']);

		// saves the page in database
		$this->cms_add->title = $form_result['CMStitre'];
		$this->cms_add->content = '<h2>'.pSQL($form_result['CMStitre'])
			.'</h2> <a id= "iframeCms" href= "'.$detail_from->preview_location.'" >'.
			$this->l('do not delete auto generated code').'</a>';
		$this->cms_add->addInDB();

		// Saved message
		$this->message = $this->setMessage('CMS page added!','valid');
	}

	/**
	 * saves form settings (iframe)
	 */
	private function configureFormPosition()
	{
		$this->data = array ();

		// Get settings
		$form_settings = unserialize(Configuration::get(Constants::FORM_STTGS));

		// prepare the array
		$hooks = array (
				'old' => ($form_settings) ? $form_settings['hooks'] : false,
				'new' => (Tools::isSubmit('hooks')) ? Tools::getValue('hooks') : false
		);

		if(! $this->checkSubmitFormPosition($hooks))
		{
			return;
		}

		// hide everything
		foreach ($this->hooks as $hook)
			if ($this->isRegisteredInHook('display'.$hook['hook']))
			{
				$this->unregisterHook('display'.$hook['hook']);
			}
				
		// show selected hooks
		if ($hooks['new'])
		{
			foreach ($hooks['new'] as $hook)
			{
				$this->registerHook('display'.$hook);
			}
				
		}
			
		// saves the new settings
		if (Configuration::updateValue(Constants::FORM_STTGS, serialize(array (
				'data' => $this->data,
				'hooks' => $hooks['new']
		))))
		{
			$this->message = $this->setMessage('Saved!','valid');
		}
	}

	/**
	 * save settings for change segment on event
	 */
	private function configureFormEvents()
	{
		$array_to_save = array();
		foreach ($this->action_hooks as $hook => $details)
			if (!empty($details) && Tools::isSubmit($hook.'choixSegment'))
			{
				//fill array to save
				$array_to_save[$hook]['champs'] = array();
				$array_to_save[$hook]['segment'] = Tools::getValue($hook.'choixSegment');
				foreach ($details['fields'] as $name => $tab)
					if (isset($tab) && Tools::isSubmit($hook.'champs'.$name))
					{
						$array_to_save[$hook]['champs'][$name] = Tools::getValue($hook.'champs'.$name);
					}
			}
		Configuration::updateValue(Constants::EVENT_STTGS, serialize($array_to_save));
		// Saved message
		$this->message = $this->setMessage('Saved!','valid');
	}

	/**
	 * save settings for abandonned cart
	 */
	private function configureCartEvents()
	{
		// Saved message
		$this->message = $this->setMessage('Saved!','valid');
		$array_to_save = array();

		if (Tools::isSubmit('submitMailPerfCartEvent'))
		{
			$array_to_save['isValidate'] = Tools::getValue('activateCart');
			//for each fields needed

			foreach ($this->abandonned_cart_hooks as $name => $details)
				if (!empty($details) && Tools::isSubmit('cart'.$name))
				{
					if (!in_array(Tools::getValue('cart'.$name), $array_to_save)) //check if the field is not bind already
					{
						$array_to_save[$name] = Tools::getValue('cart'.$name);
					}	
					else
					{
						$this->message = $this->setMessage('you can\'t select twice the same field.','error');
						return;//exit on error
					}
				}
				else
                {
					$this->message = $this->setMessage(' is empty!','error',1,$details);
					return; //exit on error
				}
		}

		Configuration::updateValue(Constants::EVENT_CART_STTGS, serialize($array_to_save));
	}

	/**
	 * saves new target on subscribe settings
	 */
	private function configureFormImport()
	{
		$this->message = $this->setMessage('Saved!','valid');

		$fields = $this->mperf->fields->getListFields();
		$oblig_fields = array();
		foreach ($fields as $f)
		{
			if ($f->is_obligatory || $f->is_unicity)
			{
				$oblig_fields[$f->id] = $f;
			}
		}

		$field_name = 'dbSelect';
		$fiellink_name = $field_name.'Link';

		$dbfields = $this->sendThisInformation()['DBfield'];
		$import_bind = array ();
		$import_bind['fields'] = array ();
		$import_bind['isAutoSync'] = Tools::getValue('isAutoSync') == 'on';
		$import_bind['isAddNoNews'] = Tools::getValue('isAddNoNews') == 'on';
		$import_bind['inSegmentId'] = Tools::getValue('choixSegment');

		if (($new_segment_id = $this->checkNewSegment()) != -1)
		{
			$import_bind['inSegmentId'] = $new_segment_id;
		}
		
		// get all fields and saves bindings
		foreach ($dbfields as $dbfield)
		{
			np6Utils::bindField($field_name, $fiellink_name , $dbfield, $import_bind, $oblig_fields);
		}

		if (count($oblig_fields) > 0)
		{
			$this->message = $this->setMessage('All unicity or obligatory fields are not bind.','warning');
		}

		// saves settings
		Configuration::updateValue(Constants::IMPORT_STTGS, serialize($import_bind));
	}

	/**
	 * check if in the import tab we add a new segment
	 *
	 * @return new segment id or -1 if error
	 */
	private function checkNewSegment()
	{
		// if we click on the add segment button
		if (Tools::isSubmit('submitMailPerfFormImportAddSegment'))
		{
			$this->message = $this->setMessage('New segment added!','valid');

			if (Tools::isSubmit('newSegmentName') && Tools::isSubmit('newSegmentDesc')
					&& Tools::isSubmit('newSegmentDate') && !empty(Tools::getValue('newSegmentName'))
					&& !empty(Tools::getValue('newSegmentDesc')) && !empty(Tools::getValue('newSegmentDate')))
			{
				$date = strtotime(Tools::getValue('newSegmentDate'));
				if ($date != 0)
				{
					// create and add an new segment
					$segment = new Segment();
					$segment->type = TypeSegment::STATIC_SEGMENT;
					$segment->name = Tools::getValue('newSegmentName');
					$segment->description = Tools::getValue('newSegmentDesc');
					$segment->expiration_date = Tools::getValue('newSegmentDate');
					$segment->for_test = false;
					$result_segment = $this->mperf->segments->createSegment($segment);

					// check the success
					if ($result_segment != null)
					{
						return $result_segment->id;
					}
						
					$this->message = $this->setMessage('Failed to save the segment!','warning',2);
				}
				else
				{
					$this->message = $this->setMessage('Date format is not valid!','error');
				}
			}
			else
			{
				$this->message = $this->setMessage('All fields are required!','error');
			}
		}
		return -1;
	}

	/**
	 * Check if form values are valid
	 *
	 * @return bool
	 */
	private function checkSubmitFormPosition($hooks)
	{
		$this->message = $this->setMessage('Saved!','valid'); // if no error

		// if nothing is selected
		/*
		if (!$hooks['new'])
		{
			$this->message = $this->setMessage('Select a form position','error'); 
			return false;
		}
		*/

		// if a form is selected
		if (Tools::isSubmit('formSelection') && !empty(Tools::getValue('formSelection')))
		{
			$this->data['idForm'] = Tools::getValue('formSelection');
		}			
		else
		{
			$this->data['idForm'] = false;
			$this->message = $this->setMessage('Select a form.','error'); 
			return false;
		}

		// get the form link
		$detail_from = $this->mperf->forms->getDetailFormById($this->data['idForm']);

		if ($detail_from != null)
		{
			$this->data['formLink'] = $detail_from->preview_location;
		}
		else
		{
			$this->message = $this->setMessage('API Error .','error'); 
		}
			

		// if the height is not null
		if (Tools::isSubmit('hauteurFrame'))
		{
			$this->data['hauteur'] = Tools::getValue('hauteurFrame') > 1 ? Tools::getValue('hauteurFrame') : 'auto';
		}	
		else
		{
			$this->data['hauteur'] = 'auto';
		}
			
		// show form or button
		$this->data['showForm'] = Tools::isSubmit('showForm') ? (Tools::getValue('showForm') == 'on') : false;

		// if the text of the button is not empty
		if (Tools::isSubmit('textBouton'))
		{
			$this->data['textBouton'] = htmlspecialchars(Tools::getValue('textBouton'));
		}			
		elseif (!$this->data['showForm'])
		{
			$this->message = $this->setMessage('Your button has no text.','warning');
			$this->data['textBouton'] = $this->l('Newsletter');
		}
		else
			$this->data['textBouton'] = ' ';

		// check the title
		$this->data['title'] = Tools::isSubmit('formtitle') ? htmlspecialchars(Tools::getValue('formtitle')) : ' ';

		return true;
	}

	/**
	 * check if forms are submit and execute there methods
	 */
	private function verifFormSubmit()
	{
		if (Tools::isSubmit('submitMailPerfAuth'))
		{
			$this->configureAuthNp6();
			$this->tab_index_to_open = 0;
		}
		elseif (Tools::isSubmit('submitMailPerfFormPosition'))
		{
			$this->configureFormPosition();
			$this->tab_index_to_open = 2;
		}
		elseif (Tools::isSubmit('submitMailPerfFormImportAddSegment'))
		{
			$this->configureFormImport();
			$this->tab_index_to_open = 1;
		}
		elseif (Tools::isSubmit('submitMailPerfFormImport'))
		{
			$this->configureFormImport();
			$this->tab_index_to_open = 1;
		}	
		elseif (Tools::isSubmit('submitMailPerfFormPage'))
		{
			$this->configureFormPage();
			$this->tab_index_to_open = 2;
		}
		elseif (Tools::isSubmit('submitMailPerfFormEvent'))
		{
			$this->configureFormEvents();
			$this->tab_index_to_open = 3;
		}
		elseif (Tools::isSubmit('submitMailPerfCartEvent'))
		{
			$this->configureCartEvents();
			$this->tab_index_to_open = 4;
		}
	}

	/**
	 * check if a form is submit
	 *
	 * @return bool
	 */
	private function isSubmit()
	{
		return Tools::isSubmit('submitMailPerfAuth') || Tools::isSubmit('submitMailPerfFormPage')
		|| Tools::isSubmit('submitMailPerfFormPosition') || Tools::isSubmit('submitMailPerfFormImport');
	}

	/**
	 * Backoffice javascript et CSS link
	 */
	public function hookDisplayBackOfficeHeader()
	{
		// CSS
		$this->context->controller->addCSS($this->_path.'css/admin.css');
		// JS
		$this->context->controller->addJS($this->_path.'js/message.js');
		$this->context->controller->addJS($this->_path.'js/form.js');
		$this->context->controller->addJS("//code.jquery.com/jquery-1.10.2.js");
		$this->context->controller->addJS("//code.jquery.com/ui/1.11.4/jquery-ui.js");
		$this->context->controller->addJS($this->_path.'js/jquerydepend.js');
	}

	/**
	 * Client CSS and JS
	 */
	public function hookDisplayHeader()
	{
		// CMS page iframe javascript
		$this->context->controller->addCSS($this->_path.'css/frame.css');
		$this->context->controller->addJS($this->_path.'js/frame.js');
	}

	/**
	 * Top of pages hook
	 */
	public function hookDisplayTop($params)
	{
		return $this->hookDisplayHome($params, 'top');
	}

	/**
	 * show the iframe form
	 */
	public function hookDisplayHome($params, $class = false)
	{
			// get options
			$this->data = unserialize(Configuration::get(Constants::FORM_STTGS));
			if (!$this->data && is_array($params))
				return;

			$smarty = array ();
			$smarty['class'] = ($class) ? $class : 'home';
			$smarty['data'] = ($this->data['data']) ? $this->data['data'] : array (
					'title' => '',
					'form' => '',
					'idForm' => ''
			);

			$this->smarty->assign('mPerfForm', $smarty);
			return $this->display(__FILE__, 'views/templates/hook/home.tpl');
	}

	/**
	 * Left Column Hook
	 */
	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayHome($params, 'right');
	}

	/**
	 * Right Column Hook
	 */
	public function hookDisplayLeftColumn($params)
	{
		return $this->hookDisplayHome($params, 'left');
	}

	/**
	 * Footer hook
	 */
	public function hookDisplayFooter($params)
	{
		return $this->hookDisplayHome($params, 'footer');
	}

	/**
	 * Product page hook
	 */
	public function hookDisplayLeftColumnProduct($params)
	{
		return $this->hookDisplayHome($params, 'left-product');
	}

	/**
	 * Product page hook
	 */
	public function hookDisplayRightColumProduct($params)
	{
		return $this->hookDisplayHome($params, 'right-product');
	}

	/**
	 * Product page hook
	 */
	public function hookDisplayFooterProduct($params)
	{
		return $this->hookDisplayHome($params, 'footer-product');
	}

	/**
	 * Show target post errors on the dashboard
	 */
	public function hookDashboardZoneOne($params)
	{
		// get configuration values and create the smarty array
		// $import_bind = unserialize(Configuration::get(Constants::IMPORT_STTGS));
		$smarty_array = array ();
		// get dash board dates
		$start_date = strtotime($params['date_from']);
		$end_date = strtotime($params['date_to']) + 24 * 60 * 60;

		// get all error infos
		$sql = 'SELECT * FROM '._DB_PREFIX_.$this->db_name_target_error." WHERE errorTimestamp >= '$start_date' AND errorTimestamp <= '$end_date' ";
		if ($results = Db::getInstance()->ExecuteS($sql))
			$smarty_array['errorTable'] = $results;

		$this->context->smarty->assign($smarty_array);
		return $this->display(__FILE__, 'views/templates/admin/dashboard_zone_one.tpl');
	}

	/**
	 * dashboard datas
	 */
	public function hookDashboardData($params)
	{
		// no need
	}

	/**
	 * execute when the customer create an account
	 *
	 * @param mixed $params
	 */
	public function hookActionCustomerAccountAdd($params)
	{
	    // if no new customer, do nothing
	    if (!isset($params['newCustomer']))
	        return;

	    $customer = $params['newCustomer'];

	    // if no key or connection do nothing
		$apikey = unserialize(Configuration::get(Constants::SETTINGS_STR));
	    if (!isset($apikey) || !$this->apiConnexion($apikey['alkey']))
	        return;

		$send_array = $this->getCustomerBindArray($customer);

		// if no data to send to MPerf, do nothing
		if($send_array == null)
		    return;

		// check target has not already been created by another hook
		$id_mp = null;
		$sql = 'SELECT * FROM '._DB_PREFIX_.$this->db_name_mp_link.' WHERE idPresta="'.$customer->id.'" LIMIT 1';
		if ($results = Db::getInstance()->ExecuteS($sql))
			foreach ($results as $row)
				$id_mp = $row['idMP'];

		if (!$id_mp)
		{
			$this->CreateLinkPrestashopMailForce($send_array, $customer, $id_mp);
		}

		$import_bind = unserialize(Configuration::get(Constants::IMPORT_STTGS));
		// add the new target in the selected segment
		if ($import_bind != null && isset($import_bind['inSegmentId']) && $import_bind['inSegmentId'] != -1)
		{
			$segment_result = $this->mperf->segments->setTargetInSegment($import_bind['inSegmentId'], $id_mp);

			// if error, save it
			if(!$segment_result)
			{
				Db::getInstance()->insert($this->db_name_target_error, array (
					'customer_Id' => $customer->id,
				    'errorText' => str_replace('\\', '\\\\', __FILE__).':'.__METHOD__.':'.__LINE__.' '.$this->mperf->segments->erreur,
				    'errorTimestamp' => time()));
			}
		}

	}

	/**
	 * execute when the customer update his values
	 * @param mixed $params
	 */
	public function hookActionCustomerAccountUpdate($params)
	{
		$sql_result = Db::getInstance()->getRow(
				'SELECT * FROM '._DB_PREFIX_.$this->db_name_mp_link.
				' WHERE idPresta= '.pSQL($params['customer']->id));
		if ($sql_result && isset($sql_result['idMP']) && !empty($sql_result['idMP'])) //if entry exist
		{
			$apikey = unserialize(Configuration::get(Constants::SETTINGS_STR));
			if (isset($apikey) && $this->apiConnexion($apikey['alkey']) && isset($params['customer'])) // if connexion go on
			{
				$send_array = $this->getCustomerBindArray($params['customer']);

				// saves target in mailPerformance
				$this->mperf->targets->updateTargetFromValues($sql_result['idMP'], $send_array);
			}
		}
		else //if the user is not linked to MailPerf
			$this->hookActionCustomerAccountAdd(array('newCustomer' => $params['customer']));
	}

	/**
	 * get the target values array this binds
	 * @param Customer $new_customer
	 * @return array|null
	 */
	private function getCustomerBindArray($new_customer)
	{

		$import_bind = unserialize(Configuration::get(Constants::IMPORT_STTGS));


		//use to know if we are in user creation;
		$dateInscription = (new DateTime($new_customer->date_add))->getTimestamp();
		$dateNow = (new DateTime())->getTimestamp();
		$isUserCreation =  (10 > ($dateNow - $dateInscription)) ;

		// if automatic synronisation between Presta and MailPerf is not active, do nothing
		if(!$import_bind['isAutoSync'] && $isUserCreation)
		{
			return null;
		}

		// if the customer has not subscribed to the newsletter and we have not chosen to import non-subscribers, return
		if(!$new_customer->newsletter && !$import_bind['isAddNoNews'] && $isUserCreation)
		{
		    return null;
		}

		$send_array = array();

		// get all fields
		$fields = $this->mperf->fields->getIndexedFields();


		// initalize all fields to insert to null
		foreach ($fields as $field_id => $field)
		{
			$send_array[$field_id] = $field->getnullValue();
		}

		// add a 0 if null
		if ($new_customer->optin == null || empty($new_customer->optin))
			$new_customer->optin = 0;

		//todo : bind on redlist
		if ($new_customer->newsletter == null || empty($new_customer->newsletter))
			$new_customer->newsletter = 0;

		// check new customers values and insert them in the array
		foreach ($new_customer as $key => $new_user_value)
		{
		    $key = ($key == 'id') ? 'id_customer' : $key; // map customer key id -> id_customer (TODO map all to dbfield)

			// if a binding exists for the customer field
			if (isset($import_bind['fields'][$key]) && isset($import_bind['fields'][$key]['apiFieldId']) && $import_bind['fields'][$key]['apiFieldId'] != 0)
			{
				switch ($fields[$import_bind['fields'][$key]['apiFieldId']]->type)
				{
					case TypeField::CHECKBOX :
						if (isset($import_bind['fields'][$key]['binding']) && isset($import_bind['fields'][$key]['binding'][$new_user_value]) && $import_bind['fields'][$key]['binding'][$new_user_value] >= 0)
							$send_array[$import_bind['fields'][$key]['apiFieldId']][] = $import_bind['fields'][$key]['binding'][$new_user_value];
						break;
					case TypeField::RADIOBUTTON :
						if (isset($import_bind['fields'][$key]['binding']) && isset($import_bind['fields'][$key]['binding'][$new_user_value]) && $import_bind['fields'][$key]['binding'][$new_user_value] >= 0)
							$send_array[$import_bind['fields'][$key]['apiFieldId']] = $import_bind['fields'][$key]['binding'][$new_user_value];
				    	break;
					case TypeField::LISTE :
						if (isset($import_bind['fields'][$key]['binding']) && isset($import_bind['fields'][$key]['binding'][$new_user_value]) && $import_bind['fields'][$key]['binding'][$new_user_value] >= 0)
							$send_array[$import_bind['fields'][$key]['apiFieldId']] = $import_bind['fields'][$key]['binding'][$new_user_value];
				    	break;
					case TypeField::DATE :
						if($new_user_value == null)
						{
							$send_array[$import_bind['fields'][$key]['apiFieldId']] = null;
						}
						else
						{
							$send_array[$import_bind['fields'][$key]['apiFieldId']] = Field::getFormatDate(strtotime($new_user_value));
						}
						break;
					case TypeField::NUMERIC :
					    $send_array[$import_bind['fields'][$key]['apiFieldId']] = intval($new_user_value);
					    break;
					case TypeField::EMAIL :
					case TypeField::STRING :
					case TypeField::TEXTAREA :
					case TypeField::TEL :
						$send_array[$import_bind['fields'][$key]['apiFieldId']] = $new_user_value;
					    break;
					default :
					    break;
				}
			}
		}
    	
    	return $send_array;

	}

	/**
	 * execute when a customer update or create a cart
	 * @param mixed $params
	 */
	public function hookActionCartSave($params)
	{

	    // if no customer is logged in, do nothing
	    if (!$this->context->customer->isLogged())
	        return;

	    // if no cart, do nothing
		$cart = $params['cart'];
	    if($cart == null)
	        return;

	    // TODO This hook is called during the creation of an account, BEFORE we have had a chance to create the
	    // MPerf target, so in the end, it is THIS hook that creates the MPerf target and not the account creation
	    // hook

	    // this is account creation, don't
	    // if(Tools::getValue('create_account'))

		$event_settings = $this->getEventSettings();
		$target = null;
		if ($cart != null)
		{		
			//creation of the target
			$target = new Target();
			$target->values = array();
			if (isset($event_settings['actionCartSave']) && isset($event_settings['actionCartSave']['champs']))
			{
				//insert values in the target
				if ($this->checkInEventSettings($event_settings, 'actionCartSave', 'nbArticle'))
					$target->values[$event_settings['actionCartSave']['champs']['nbArticle']] = $cart->nbProducts();

				if ($this->checkInEventSettings($event_settings, 'actionCartSave', 'cartPrice'))
					$target->values[$event_settings['actionCartSave']['champs']['cartPrice']] = (string)$cart->getOrderTotal(true);

				if ($this->checkInEventSettings($event_settings, 'actionCartSave', 'modifDate'))
					$target->values[$event_settings['actionCartSave']['champs']['modifDate']] = Field::getFormatDate(time());
			}
			//abandonned cart
			$event_cart_param = unserialize(Configuration::get(Constants::EVENT_CART_STTGS));
			if ($event_cart_param && isset($event_cart_param['isValidate']) && $event_cart_param['isValidate'])
			{
				//last modif date set to time
				$target->values[$event_cart_param['lastModifDate']] = Field::getFormatDate(time());
				// confirmation date to null
				$target->values[$event_cart_param['confirmationDate']] = null;
			}
		}

		$this->eventHookSegmentChange($this->context->customer, 'actionCartSave', $target);

	}

	/**
	 * execute when the customer request to send his merchandise back to the store
	 * @param mixed $params
	 */
	public function hookActionOrderReturn($params)
	{
		$order_return = $params['orderReturn'];
		$event_settings = $this->getEventSettings();
		$target = null;
		if ($order_return != null && isset($event_settings['actionOrderReturn']) && isset($event_settings['actionOrderReturn']['champs']))
		{
			//creation of the target
			$target = new Target();
			$target->values = array();

			if ($this->checkInEventSettings($event_settings, 'actionOrderReturn', 'reason'))
					$target->values[$event_settings['actionOrderReturn']['champs']['reason']] = $order_return->question;

			if ($this->checkInEventSettings($event_settings, 'actionOrderReturn', 'modifDate'))
					$target->values[$event_settings['actionOrderReturn']['champs']['modifDate']] = Field::getFormatDate(time());
		}

		if ($this->context->customer->isLogged())
			$this->eventHookSegmentChange($this->context->customer, 'actionOrderReturn', $target);
	}

	/**
	 * execute during the new order creation process
	 * @param mixed $params
	 */
	public function hookActionValidateOrder($params)
	{
		$target = null;
		$event_settings = $this->getEventSettings();
		$order = $params['order'];
		if ($order != null)
		{
			$target = new Target();
			$target->values = array();
			if (isset($event_settings['actionValidateOrder']) && isset($event_settings['actionValidateOrder']['champs']))
			{
				//insert values in the target
				if ($this->checkInEventSettings($event_settings, 'actionValidateOrder', 'moyenPayement'))
					$target->values[$event_settings['actionValidateOrder']['champs']['moyenPayement']] = $order->payment;

				if ($this->checkInEventSettings($event_settings, 'actionValidateOrder', 'totalPaid'))
					$target->values[$event_settings['actionValidateOrder']['champs']['totalPaid']]
							= (string)$order->total_paid_real.$params['currency']->name;

				if ($this->checkInEventSettings($event_settings, 'actionValidateOrder', 'modifDate'))
					$target->values[$event_settings['actionValidateOrder']['champs']['modifDate']] = Field::getFormatDate(time());
			}
			//abandonned cart
			$event_cart_param = unserialize(Configuration::get(Constants::EVENT_CART_STTGS));
			if ($event_cart_param && isset($event_cart_param['isValidate']) && $event_cart_param['isValidate'])
			{
				// confirmation date to actual date
				$target->values[$event_cart_param['confirmationDate']] = Field::getFormatDate(time());
			}
		}
		if (isset($this->context->customer) && !empty($this->context->customer))
			$this->eventHookSegmentChange($this->context->customer, 'actionValidateOrder', $target);
	}

	/**
	 * execute when an order's status becomes "Payment accepted"
	 * @param mixed $params
	 */
	public function hookActionPaymentConfirmation($params)
	{
		if ($params == null)
			return;
		$event_settings = $this->getEventSettings();
		$target = null;
		$event_settings = $this->getEventSettings();
		if (isset($event_settings['actionPaymentConfirmation']) && isset($event_settings['actionPaymentConfirmation']['champs']))
		{
			$target = new Target();
			$target->values = array();

			if ($this->checkInEventSettings($event_settings, 'actionPaymentConfirmation', 'modifDate'))
				$target->values[$event_settings['actionPaymentConfirmation']['champs']['modifDate']] = Field::getFormatDate(time());
		}

		if ($this->context->customer->isLogged())
			$this->eventHookSegmentChange($this->context->customer, 'actionPaymentConfirmation', $target);
	}

	/**
	 * execute when an item is deleted from an order
	 * @param mixed $params
	 */
	public function hookActionProductCancel($params)
	{
		if ($params == null)
			return;
		$event_settings = $this->getEventSettings();
		$target = null;
		if (isset($event_settings['actionProductCancel']) && isset($event_settings['actionProductCancel']['champs']))
		{
			$target = new Target();
			$target->values = array();

			if ($this->checkInEventSettings($event_settings, 'actionProductCancel', 'modifDate'))
				$target->values[$event_settings['actionProductCancel']['champs']['modifDate']] = Field::getFormatDate(time());

		}
		if ($this->context->customer->isLogged())
			$this->eventHookSegmentChange($this->context->customer, 'actionProductCancel', $target);
	}

	/**
	 * Change the segment of a target when go into an action hook
	 * @param mixed $customer_id Prestashop customer id
	 * @param string $hook executed action hook
	 * @param Target $upload_target = null Target to update
	 */
	public function eventHookSegmentChange($customer, $hook, Target $upload_target = null)
 	{
		$id_mp = null;
		$sql = 'SELECT * FROM '._DB_PREFIX_.$this->db_name_mp_link.' WHERE idPresta="'.$customer->id.'" LIMIT 1';

		if ($results = Db::getInstance()->ExecuteS($sql))
		{
			foreach ($results as $row)
			{
				$id_mp = $row['idMP'];
			}
		}

		if ($id_mp == null || empty($id_mp))// saves target in mailPerformance if not exist
		{

			$send_array = $this->getCustomerBindArray($customer);

			//if we are in user creation && Synch presta/mailforce or no newsletter selected , return
			if($send_array ==  null)
			{
				return;
			}

			$this->CreateLinkPrestashopMailForce($send_array, $customer, $id_mp);
		}

		if ($id_mp != null && !empty($id_mp))
		{
			//save in the new segment
			$eventparam = $this->getEventSettings();

			if ($eventparam && isset($eventparam[$hook]) && $eventparam[$hook]['segment'] != -1)
			{
				$segment_result = $this->mperf->segments->setTargetInSegment($eventparam[$hook]['segment'], $id_mp);

				// if error, save it
				if(!$segment_result)
				{
					Db::getInstance()->insert($this->db_name_target_error, array (
						'customer_Id' => $customer->id,
					    'errorText' => str_replace('\\', '\\\\', __FILE__).':'.__METHOD__.':'.__LINE__.' '.$this->mperf->segments->erreur.' segment:'.$eventparam[$hook]['segment'].' target:'.$id_mp,
					    'errorTimestamp' => time()));
				}
			}

			//update target values
			if ($upload_target != null)
			{
				$upload_target->id = $id_mp;;
				$target_result = $this->mperf->targets->updateTarget($upload_target);
				if(!$target_result)
				{
					Db::getInstance()->insert($this->db_name_target_error, array (
						'customer_Id' => $customer->id,
						'errorText' => str_replace('\\', '\\\\', __FILE__).':'.__METHOD__.':'.__LINE__.' '.$this->mperf->targets->erreur.' target:'.$upload_target->id.' fields:'.implode(",", $upload_target->fields),
						'errorTimestamp' => time()));
				}
			}
		}
	}

	/**
	 * Generic connection error message
	 */
	private function messageConnexionError()
	{
		//to not overide another error
		if ($this->message != null && isset($this->message['type']) && $this->message['type'] == 'error')
			return;
		$this->message = array (
				'text' => $this->l('Connection error! '),
				'type' => 'error'
		);
	}

	/**
	 * get event settings
	 */
	private function getEventSettings()
	{
		return unserialize(Configuration::get(Constants::EVENT_STTGS));
	}

	/**
	 * check if the field exist
	 * @param event settings configuration array
	 * @param hook name
	 * @param field to check
	 *
	 */
	private function checkInEventSettings($event_settings, $hook, $field)
	{
		return isset($event_settings[$hook]['champs'][$field])
				&& $event_settings[$hook]['champs'][$field] > 0;
	}

	public function setMessage($text, $type ,$specialCase = 0,$details ="")
	{
		if($specialCase == 0) //default setMessage
		{
			return array (
				'text' => $this->l($text),
				'type' => $type
			);
		}
		else if($specialCase == 1) // use specifically
		{
			return array (
						'text' => $details['text'].$this->l($text),
						'type' => $type
					);
		}
		else if($specialCase == 2) // use specifically
		{
			return array (
						'text' => $this->l($text).'<br>'.print_r($this->mperf->segments->erreur),
						'type' => $type
					);
		}
	}

	private function sendThisInformation()
	{
		$languages = Language::getLanguages(false);

		// new array , contain $this needed information
		$array_temp = array(
			'admin_tpl_path' =>  $this->admin_tpl_path,
			'hooks_tpl_path' =>  $this->hooks_tpl_path,
			'module' =>  $this->name,
			'admin_tpl_path' =>  $this->admin_tpl_path,
			'admin_tpl_path' =>  $this->admin_tpl_path,
			'version' => $this->version,
			'context' => $this->context,
			'tab' => $this->tab,
			'hooks' => $this->hooks,
			'action_hooks' => $this->action_hooks,
			'cart_hooks' => $this->abandonned_cart_hooks,
			'isConnected' => $this->is_connected,
			'DBfield' => null ,
			'flags' => array (
						'title' => $this->displayFlags($languages, $this->context->language->id, 'title¤form', 'title', true),
						'form' => $this->displayFlags($languages, $this->context->language->id, 'title¤form', 'form', true)
				),
			'mperf' => $this->mperf,
			'apiError' => $this->l('API Error!'),
			'Mr.' => $this->l('Mr.'),
			'Ms.' => $this->l('Ms.'),
			'Title' => $this->l('Title'),
			'First name' => $this->l('First name'),
			'Last name' => $this->l('Last name'),
			'E-mail' => $this->l('E-mail'),
			'Birthdate' => $this->l('Birthdate'),
			'no' => $this->l('no'),
			'yes' => $this->l('yes'),
			'Subscribe to the newsletter date' => $this->l('Subscribe to the newsletter date'),
			'Third party offers' => $this->l('Third party offers'),
			'cms_page_list' => $this->cms_page_list,
			);
	
		//set DBfield 
		$array_temp['DBfield'] = np6Utils::getDBfield($array_temp);

		return $array_temp;

	}

	// create the row for link Prestashop client to mailforce target
	public function CreateLinkPrestashopMailForce($send_array, $customer, & $id_mp)
	{
			$target_result = $this->mperf->targets->createTarget($send_array);

			if($target_result)
			{
				$id_mp = $target_result->id;

				Db::getInstance()->insert($this->db_name_mp_link, array(
				'idMP' => $target_result->id,
				'idPresta' => $customer->id));

			}
			else // if error, save and quit
			{
				Db::getInstance()->insert($this->db_name_target_error, array (
						'customer_Id' => $customer->id,
						'errorText' => str_replace('\\', '\\\\', __FILE__).':'.__METHOD__.':'.__LINE__.' '.$this->mperf->targets->erreur,
						'errorTimestamp' => time()
					));

				return ;
			}
	}

}
