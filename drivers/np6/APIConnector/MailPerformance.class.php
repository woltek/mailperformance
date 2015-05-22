<?php
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');

class MailPerformance
{

	/**
	 * actual user id
	 * @var string
	 */
	var $user_id;

	/**
	 * actual customer id
	 * @var string
	 */
	var $customer_id;

	/**
	 * agence du contact
	 * @var string
	 */
	var $agency_id;

	/**
	 * auto login key
	 * @var string
	 */
	var $auto_login_key;

	/**
	 * x-key
	 * @var string
	 */
	var $xkey;

	/**
	 * contact identifier
	 * @var string
	 */
	var $login;

	/**
	 * contact password
	 * @var string
	 */
	var $password;

	/**
	 * client to connect to REST API
	 */
	var $rest_client;

	/**
	 * don't save any cache or cookie
	 * @var boolean
	 */
	var $clear_cache = false;

	/**
	 * Category connector
	 * @var CategoryConnector
	 */
	var $category;

	/**
	 * Contacts connector
	 * @var ContactsConnector
	 */
	var $contacts;

	/**
	 * Fields connector
	 * @var FieldsConnector
	 */
	var $fields;

	/**
	 * Forms connector
	 * @var FormsConnector
	 */
	var $forms;

	/**
	 * Segments connector
	 * @var SegmentsConnector
	 */
	var $segments;

	/**
	 * Targets connector
	 * @var TargetsConnector
	 */
	var $targets;

	/**
	 * Value lists connector
	 * @var ValueListsConnector
	 */
	var $value_lists;

	/**
	 * connect to the API and initialize connector
	 * if one parameter auto login key
	 * else if two parameters login and password
	*/
	public function __construct()
	{
		$this->rest_client = new RequestRest();

		// check arguments
		$ctp = func_num_args();
		$args = func_get_args();
		if ($ctp == 1)
		{
			if (Tools::strlen($args[0]) == 112)
			{
				$this->auto_login_key = $args[0];
				$this->authFromAlk();
			}
			else
			{
				$this->xkey = $args[0];
				$this->rest_client->xkey = $args[0];
			}
		}
		if ($ctp == 2)
		{
			$this->login = $args[0];
			$this->password = $args[1];
			$this->authFromCredentials();
		}

		$this->category = new CategoryConnector($this->rest_client);
		$this->contacts = new ContactsConnector($this->rest_client);
		$this->fields = new FieldsConnector($this->rest_client);
		$this->forms = new FormsConnector($this->rest_client);
		$this->segments = new SegmentsConnector($this->rest_client);
		$this->targets = new TargetsConnector($this->rest_client);
		$this->value_lists = new ValueListsConnector($this->rest_client);
	}

	/**
	 * Authenticate to API
	 * @return boolean true if success
	 */
	public function authFromAlk()
	{
		if ($this->auto_login_key == null)
			return false;

		$post_content = array(
			'method' => array(
				'name' => 'authenticateFromAutoLoginKey',
				'version' => 1
			),
			'parameters' => array(
				'alKey' => $this->auto_login_key
			),
			'debug' => array(
				'forceSync' => true
			)
		);

		// create a POST request for the connection
		$this->rest_client->clear_cache = true;
		list($result, $error) = $this->rest_client->post('/api/auth', $post_content);
		$this->rest_client->clear_cache = false;

		if ($error)
			return false;

		// check connection et get user infos
		if ($result && APIConnector::verifNoError($result) == '')
		{
			$this->getIdentityInfo($result);
			return $result['state'] == 'success' || $result['response']['verdicts'][1]['access'] == 'Granted';
		}
		return false;
	}

	/**
	 * athenticate this login and password
	 * @return boolean
	 */
	public function authFromCredentials()
	{
		if ($this->login == null || $this->password == null)
			return false;

		$post_content = array(
			'method' => array(
				'name' => 'authenticateFromCredentials',
				'version' => 1
			),
			'parameters' => array(
				'login' => $this->login,
				'password' => $this->password
			),
			'debug' => array(
				'forceSync' => true
			)
		);

		// create a POST request for the connection
		$this->rest_client->clear_cache = true;
		list($result, $error) = $this->rest_client->post('/api/auth', $post_content);
		$this->rest_client->clear_cache = false;

		if ($error)
			return false;

		// check connection et get user infos
		if ($result && APIConnector::verifNoError($result) == '')
		{
			$this->getIdentityInfo($result);
			return $result['state'] == 'success' || $result['response']['verdicts'][1]['access'] == 'Granted';
		}
		return false;
	}

	private function getIdentityInfo($result)
	{
		if (isset($result['response']['identity']))
		{
				$this->user_id = $result['response']['identity']['contact'];
				$this->customer_id = $result['response']['identity']['customer'];
				$this->agency_id = $result['response']['identity']['agency'];
		}
	}

	public function setAutoLoginKey($key, $clearcache = false)
	{
		if (Tools::strlen($key) == 112)
		{
			$this->auto_login_key = $args[0];
			$this->clear_cache = $clearcache;
			return $this->authFromAlk();
		}
		else
		{
			$this->clear_cache = $clearcache;
			$this->xkey = $args[0];
			$this->rest_client->xkey = $args[0];
			return true;
		}
	}
}