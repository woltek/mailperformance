<?php

// require_once 'C:\wamp\www\prestashop\prestashop\modules\np6\Ajax.php';
require_once '/../../../../bootstrap.php';


/**
 * Test class for np6.
 * Generated by PHPUnit on 2014-08-04 at 11:47:35.
 */
class np6Test extends ModulePrestaShopPHPUnit
{

	var $mockedFormConnector;

	var $mockedFieldConnector;

	var $mockedContactConnector;

	var $mockedSegmentConnector;

    var $mockedTargetConnector;

	var $cmsListMocked;

	/**
	 * @covers np6::getContent
	 * @covers np6::createASmartyArray
	 * @covers np6::verifFormSubmit
	 * @covers np6::messageConnexionError
	 */
	public function testGetContent()
	{
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->getContent();
		$this->assertContains('<form id="form-auth-np6" class="col-md-6" method="post"', $result);
	}

	/**
	 * @covers np6::hookDisplayHome
	 * @covers np6::hookDisplayTop
	 */
	public function testHookDisplayTop()
	{
		$this->removeHookConfFile();
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDisplayTop(array('value'));
		$this->assertNull($result);
	}

	/**
	 * @covers np6::hookDisplayHome
	 * @covers np6::hookDisplayRightColumn
	 */
	public function testHookDisplayRightColumn()
	{
		$this->removeHookConfFile();
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDisplayRightColumn(array('value'));
		$this->assertNull($result);
	}

	/**
	 * @covers np6::hookDisplayHome
	 * @covers np6::hookDisplayLeftColumn
	 */
	public function testHookDisplayLeftColumn()
	{
		$this->removeHookConfFile();
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDisplayLeftColumn(array('value'));
		$this->assertNull($result);
	}

	/**
	 * @covers np6::hookDisplayHome
	 * @covers np6::hookDisplayFooter
	 */
	public function testHookDisplayFooter()
	{
		$this->removeHookConfFile();
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDisplayFooter(array('value'));
		$this->assertNull($result);
	}

	/**
	 * @covers np6::hookDisplayHome
	 */
	public function testHookDisplayHome()
	{
		$this->displayHookForm();
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDisplayHome(array('value'));
		$this->assertContains('titre', $result);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::hookDisplayHome
	 * @covers np6::hookDisplayLeftColumnProduct
	 */
	public function testHookDisplayLeftColumnProduct()
	{
		$this->removeHookConfFile();
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDisplayLeftColumnProduct(array('value'));
		$this->assertNull($result);
	}

	/**
	 * @covers np6::__construct
	 */
	public function testConstruct()
	{
		$result = new np6();
		$this->assertEquals('np6', $result->name);
	}

	/**
	 * @covers np6::hookDisplayHome
	 * @covers np6::hookDisplayRightColumProduct
	 */
	public function testHookDisplayRightColumProduct()
	{
		$this->removeHookConfFile();
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDisplayRightColumProduct(array('value'));
		$this->assertNull($result);
	}

	/**
	 * @covers np6::hookDisplayHome
	 * @covers np6::hookDisplayFooterProduct
	 */
	public function testHookDisplayFooterProduct()
	{
		$this->removeHookConfFile();
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDisplayFooterProduct(array('value'));
		$this->assertNull($result);
	}

	/**
	 * @covers np6::hookDashboardZoneOne
	 */
	public function testHookDashboardZoneOne()
	{
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDashboardZoneOne(array(
			'date_from' => '2012-03-05',
			'date_to' => '2014-03-05'
		));
		$this->assertContains('<section id="dashactivity', $result);
	}

	/**
	 * @covers np6::hookDashboardData
	 */
	public function testHookDashboardData()
	{
		$result = Module::getInstanceByName(Tools::strtolower($this->getClass()))->hookDashboardData(array());
		$this->assertNull($result);
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::apiConnexion
	 * @covers np6::messageConnexionError
	 */
	public function testGetContent_wrongAlkey()
	{
		$this->addHookConfFile();
		$this->mockConnectors();
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = "error Message";
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;

		$result = $class->getContent();
		$this->assertContains('<form id="form-auth-np6" class="col-md-6" method="post"', $result);
		$this->assertContains('<div id="messagebloc" class="messagebloc error">', $result);
	}


	/**
	 * @covers np6::getContent
	 * @covers np6::apiConnexion
	 * @covers np6::createASmartyArray
	 * @covers np6::checkApiError
	 * @covers np6::getDBfield
	 */

	public function testGetContent_Connection()
	{
        $this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();
		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$result = $class->getContent();
		$this->assertContains('<form id="form-auth-np6" class="col-md-6" method="post"', $result);
		$this->assertContains('id="form-feed"', $result);
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureFormPosition
	 * @covers np6::checkSubmitFormPosition
	 * @covers np6::hookDisplayFooter
	 */
	public function testGetContent_FormPosition()
	{
        $this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$_POST['submitMailPerfFormPosition'] = 'Save';
		$_POST['hooks'] = array(
			'Footer'
		);
		$_POST['textBouton'] = 'NewsletterButtonText';
		$_POST['formSelection'] = '42';
		$result = $class->getContent();
		$this->assertContains('NewsletterButtonText', $result);
		$this->assertFalse(empty($class->hookDisplayFooter(array())));
		Configuration::deleteByName(Constants::FORM_STTGS);
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureFormPosition
	 * @covers np6::checkSubmitFormPosition
	 * @covers np6::hookDisplayFooter
	 */
	public function testGetContent_FormPosition_emptyValues()
	{
		$this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$_POST['submitMailPerfFormPosition'] = 'Save';
		$_POST['hooks'] = array(
			'Footer'
		);
		$_POST['idForm'] = 42;
		$result = $class->getContent();

		$this->assertTrue(empty($class->hookDisplayFooter(array())));
		Configuration::deleteByName(Constants::FORM_STTGS);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureFormImport
	 * @covers np6::checkNewSegment
	 */
	public function testGetContent_FormImport()
	{
		$this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$_POST['submitMailPerfFormImport'] = 'Save';
		$_POST['isAutoSync'] = 'on';
		$_POST['isAddNoNews'] = 'on';
		$_POST['inSegmentId'] = - 1;
		$_POST['dbSelectid_customer'] = 42;
		$_POST['isNewSegment'] = true;
		$_POST['newSegmentName'] = 'SegmentName';
		$_POST['newSegmentDesc'] = 'description';
		$_POST['newSegmentDate'] = '2032-03-03';
		$result = $class->getContent();

        (bool) $boolean = false ;
        if ( strpos($result,'<div id="messagebloc" class="messagebloc valid"')|| strpos($result,'<div id="messagebloc" class="messagebloc warning"') )
        {
            $boolean =  true ;
        }

		$this->assertEquals($boolean,true);
		Configuration::deleteByName(Constants::FORM_STTGS);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureFormEvents
	 * @covers np6::checkNewSegment
	 */
	public function testGetContent_FormEvents()
	{
		$this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$_POST['submitMailPerfFormEvent'] = 'Save';
		$_POST['actionCartSavechoixSegment'] = '52';
		$_POST['actionOrderReturn'] = '42';

		$result = $class->getContent();
		$resultconf = unserialize(Configuration::get(Constants::EVENT_STTGS));

		$this->assertContains('<div id="messagebloc" class="messagebloc valid"', $result);
		$this->assertEquals('52',$resultconf['actionCartSave']['segment']);

		Configuration::deleteByName(Constants::EVENT_STTGS);
		$this->removeHookConfFile();
	}


	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureFormImport
	 * @covers np6::checkNewSegment
	 */
	public function testGetContent_FormImport_wrongSegment()
	{
		$this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$_POST['submitMailPerfFormImport'] = 'Save';
		$_POST['isAutoSync'] = 'on';
		$_POST['isAddNoNews'] = 'on';
		$_POST['inSegmentId'] = - 1;
		$_POST['dbSelectid_customer'] = 42;
		$_POST['isNewSegment'] = true;
		$_POST['newSegmentName'] = 'SegmentName';
		$_POST['newSegmentDesc'] = '';
		$_POST['newSegmentDate'] = '';
		$result = $class->getContent();

        (bool) $boolean = false ;
        if ( strpos($result,'<div id="messagebloc" class="messagebloc error"')|| strpos($result,'<div id="messagebloc" class="messagebloc warning"') )
        {
            $boolean =  true ;
        }
        $this->assertEquals($boolean,true);

		Configuration::deleteByName(Constants::FORM_STTGS);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureAuthNp6
	 */
	public function testGetContent_configureAuthNp6()
	{
        $this->addHookConfFile();
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$_POST['submitMailPerfAuth'] = 'Save';
		$_POST['alkey'] = '73BC81AF4A5A604FCE612A0F119D9784EF60029A83CF78F60F38F0C6ADD18F9A987C1D9DF6F757D0119904F4FA8B4770392EBFD3019F457A';

		$result = $class->getContent();
		$this->assertContains('<div id="messagebloc" class="messagebloc valid"', $result);
		Configuration::deleteByName(Constants::SETTINGS_STR);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureAuthNp6
	 */
	public function testGetContent_configureAuthNp6_emptyAlKey()
	{
		$this->addHookConfFile();
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$_POST['submitMailPerfAuth'] = 'Save';
		$_POST['alkey'] = '';

		$result = $class->getContent();
		$this->assertContains('<div id="messagebloc" class="messagebloc error"', $result);
		Configuration::deleteByName(Constants::SETTINGS_STR);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureFormPage
	 */
	public function testGetContent_configureFormPage()
	{
        $this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$addcmsMocked = $this->getMock('AddCmsPage');
		$addcmsMocked->expects($this->any())
			->method('addInDB')
			->will($this->returnValue(''));
		$class->cms_add = $addcmsMocked;

		$_POST['submitMailPerfFormPage'] = 'Save';
		$_POST['CMStitre0'] = 'titre cms';
        //warning here use a form id in your database
		$_POST['CMSform0'] = '000GN4';

		$result = $class->getContent();

		$this->assertContains($_POST['CMStitre0'], $class->cms_add->content);
		$this->assertContains('<div id="messagebloc" class="messagebloc valid"', $result);

		Configuration::deleteByName(Constants::SETTINGS_STR);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureFormPage
	 */
	public function testGetContent_emptyTitle()
	{
        $this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$addcmsMocked = $this->getMock('AddCmsPage');
		$addcmsMocked->expects($this->any())
		->method('addInDB')
		->will($this->returnValue(''));
		$class->cmsAdd = $addcmsMocked;

		$_POST['submitMailPerfFormPage'] = 'Save';
		$_POST['CMStitre0'] = '';
		$_POST['CMSform0'] = '42';

		$result = $class->getContent();

		$this->assertContains('<div id="messagebloc" class="messagebloc error"', $result);

		Configuration::deleteByName(Constants::SETTINGS_STR);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::getContent
	 * @covers np6::verifFormSubmit
	 * @covers np6::configureFormPage
	 */
	public function testGetContent_emptyform()
	{
        $this->addHookConfFile(true);
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$this->mockConnectors();

		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$addcmsMocked = $this->getMock('AddCmsPage');
		$addcmsMocked->expects($this->any())
		->method('addInDB')
		->will($this->returnValue(''));
		$class->cmsAdd = $addcmsMocked;

		$_POST['submitMailPerfFormPage'] = 'Save';
		$_POST['CMStitre0'] = 'qzrhqhrqhr';
		$_POST['CMSform0'] = '';

		$result = $class->getContent();

		$this->assertContains('<div id="messagebloc" class="messagebloc error"', $result);

		Configuration::deleteByName(Constants::SETTINGS_STR);
		$this->removeHookConfFile();
	}

	/**
	 * @covers np6::hookActionCustomerAccountAdd
	 * @covers np6::apiConnexion
	 */
	public function testHookActionCustomerAccountAdd()
	{
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$customer = $this->createFakeNewCustomer();
		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$mockedTargetConnector = $this->getMock('APIConnector\TargetsConnector');

		$targetResult = new \Target();
		$targetResult->id = 777;

		$mockedTargetConnector->expects($this->any())
			->method('createTarget')
			->with($this->anything())
			->will($this->returnValue($targetResult));
		$class->targetConnector = $mockedTargetConnector;

		$result = $class->hookActionCustomerAccountAdd(array(
			'newCustomer' => $customer
		));

	}

	/**
	 * @covers np6::hookActionCustomerAccountAdd
	 * @covers np6::apiConnexion
	 */
	public function testHookActionCustomerAccountAdd_InSegment()
	{
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$customer = $this->createFakeNewCustomer(true);
		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$mockedTargetConnector = $this->getMock('APIConnector\TargetsConnector');

		$targetResult = new \Target();
		$targetResult->id = 42;

		$mockedTargetConnector->expects($this->any())
			->method('createTarget')
			->with($this->anything())
			->will($this->returnValue($targetResult));
		$class->targetConnector = $mockedTargetConnector;

		$result = $class->hookActionCustomerAccountAdd(array(
			'newCustomer' => $customer
		));
	}

	/**
	 * @covers np6::hookActionCustomerAccountAdd
	 * @covers np6::apiConnexion
	 */
	public function testHookActionEventSegmentChange()
	{
		$class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

		$customer = $this->createFakeNewCustomer(true);
		$class->form_connector = $this->mockedFormConnector;
		$class->form_connector->erreur = '';
		$class->fields_connector = $this->mockedFieldConnector;
		$class->contact_connector = $this->mockedContactConnector;
		$class->segment_connector = $this->mockedSegmentConnector;
		$class->cms_page_list = $this->cmsListMocked;

		$mockedTargetConnector = $this->getMock('APIConnector\TargetsConnector');

		$targetResult = new \Target();
		$targetResult->id = 42;

		$mockedTargetConnector->expects($this->any())
		->method('updateTarget')
		->with($this->anything())
		->will($this->returnValue($targetResult));
		$class->targetConnector = $mockedTargetConnector;

		$result = $class->eventHookSegmentChange($this->createFakeNewCustomer(true),'actionPaymentConfirmation',$targetResult);
	}


    public function testsetMessage()
    {
        $class = Module::getInstanceByName(Tools::strtolower($this->getClass()));

        //test Message type1
        $messageResult = array('text' => "test" ,
            'type' => "warning",
        );
        $this->assertEquals($messageResult,$class->SetMessage("test","warning",0));

        //test Message type2
        $detail['text'] = "test";
        $messageResult = array('text' => "testtest" ,
            'type' => "warning",
        );
        $this->assertEquals($messageResult,$class->SetMessage("test","warning",1,$detail));

    }


	// helper methods
	private function displayHookForm()
	{
		Configuration::set(Constants::FORM_STTGS, serialize(array(
			'data' => array(
				'title' => 'titre',
				'formLink' => 'http://lucas.zientek.fr',
				'showForm' => true,
				'hauteur' => 'auto'
			)
		)));
	}

	private function mockConnectors()
	{
		// field connector
		$this->mockedFieldConnector = $this->getMock('FieldsConnector');
		$this->mockedFieldConnector->expects($this->any())
			->method('getListFields')
			->will($this->returnValue(array(
			new Field(json_decode(file_get_contents(__DIR__ . '/dataSource/FieldsConnectorTest_GetFields'), true))
		)));

		// form connector
		$this->mockedFormConnector = $this->getMock('FormsConnector');
		$this->mockedFormConnector->expects($this->any())
			->method('getListFormByTypes')
			->will($this->returnValue(array(
			new Form(json_decode(file_get_contents(__DIR__ . '/dataSource/FormsConnectorTest_GetForms'), true))
		)));
		$this->mockedFormConnector->expects($this->any())
			->method('getForms')
			->will($this->returnValue(array(
			new Form(json_decode(file_get_contents(__DIR__ . '/dataSource/FormsConnectorTest_GetForms'), true))
		)));
		$detailsTab = array(
			'informations' => array(
				'previewLocation' => 'http://lucas.zientek.fr'
			)
		);
		$this->mockedFormConnector->expects($this->any())
			->method('getDetailFormById')
			->with($this->anything())
			->will($this->returnValue(new FormDetails($detailsTab, null)));

		// segment connector
		$this->mockedSegmentConnector = $this->getMock('SegmentsConnector');
		$this->mockedSegmentConnector->expects($this->any())
			->method('getSegmentByTypes')
			->with($this->anything())
			->will($this->returnValue(array(
			new Segment(json_decode(file_get_contents(__DIR__ . '/dataSource/SegmentConnectorTest_GetSegmentById'), true))
		)));

		$this->mockedSegmentConnector->expects($this->any())
			->method('createSegment')
			->with($this->anything())
			->will($this->returnValue(new Segment(json_decode(file_get_contents(__DIR__ . '/dataSource/SegmentConnectorTest_GetSegmentById'), true))));

		// cmsList
		$this->cmsListMocked = $this->getMock('CmsList');
		$this->cmsListMocked->expects($this->any())
			->method('getCmsList')
			->will($this->returnValue(false));

	}

	private function addHookConfFile($GoodKey = false )
	{
        if($GoodKey)
        {
            Configuration::set(Constants::SETTINGS_STR, serialize(array(
                'alkey' => '73BC81AF4A5A604FCE612A0F119D9784EF60029A83CF78F60F38F0C6ADD18F9A987C1D9DF6F757D0119904F4FA8B4770392EBFD3019F457A'
            )));
        }
        else {
            Configuration::set(Constants::SETTINGS_STR, serialize(array(
                'alkey' => 'unealkey'
            )));
        }
	}

	private function removeHookConfFile()
	{
		Configuration::deleteByName(Constants::SETTINGS_STR);
		Configuration::deleteByName(Constants::FORM_STTGS);
	}

	private function createFakeNewCustomer($inSegment = false)
	{
		$this->mockConnectors();

		$customer = new CustomerCore();
		$customer->optin = 1;
		$customer->email = 'trash.42@np6.com';
		$customer->newsletter = 1;
		$customer->id = 42;
		$customer->id_gender = 1;

		$settingsArray = array(
			'isAutoSync' => true,
			'fields' => array(
				'id_customer' => array(
					'apiFieldId' => '3110'
				),
				'email' => array(
					'apiFieldId' => '3100'
				),
				'id_gender' => array(
					'apiFieldId' => '3149',
					'binding' => array(
						'1' => 'man',
						'2' => 'woman'
					)
				)
			)
		);

		if ($inSegment) {
			$settingsArray['inSegmentId'] = 42;
			$this->mockedSegmentConnector->expects($this->any())
				->method('setTargetInSegment')
				->with($this->anything(), $this->anything())
				->will($this->returnValue(array(
				'success' => true
			)));
		}

		Configuration::set(Constants::IMPORT_STTGS, serialize($settingsArray));

		$listFields = array();
		$listFields['1'] = new Field();
		$listFields['1']->id = 1;
		$listFields['1']->type = TypeField::NUMERIC;
		$listFields['2'] = new Field();
		$listFields['2']->id = 2;
		$listFields['2']->type = TypeField::EMAIL;
		$listFields['3'] = new Field();
		$listFields['3']->id = 3;
		$listFields['3']->type = TypeField::LISTE;

		// mock de la fonction getIndexedField
		$this->mockedFieldConnector->expects($this->any())
			->method('getIndexedFields')
			->will($this->returnValue($listFields));

		return $customer;
	}


	// end helper methods
}

?>
