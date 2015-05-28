<?php

require_once __DIR__ . '\utils.php';
Require_once __Dir__.'\..\..\..\..\..\modules\np6\np6Utils.php';

class np6UtilsTest extends PHPUnit_Framework_TestCase
{

    public function testSetSmartArrayFieldValue()
    {
        $SmartyArray = array('empty' => "empty");

        //test add field
        $SmartyArrayResult = array('empty' => "empty",
                                    'full' => 'full');
        $this->assertEquals($SmartyArrayResult,np6Utils::SetSmartArrayFieldValue($SmartyArray,"full","full"));

         //test modify field
        $SmartyArrayResult = array('empty' => "notempty");
        $this->assertEquals($SmartyArrayResult,np6Utils::SetSmartArrayFieldValue($SmartyArray,"empty","notempty"));
    }

    public function testCheckApiError()
    {
        //test return null
        $mperf = (object)array(
            'forms' => (object)array('erreur' => ''),
            'fields' => (object)array('erreur' => ''),
            'value_lists' => (object)array('erreur' => ''),
            'contacts' => (object)array('erreur' => ''),
            'segments' => (object)array('erreur' => ''),
            );
        $param  = array('mperf' => $mperf,'apiError' => '');
        $this->assertEmpty(np6Utils::CheckApiError($param));


        //test with error ,return not null 
        $mperf = (object)array(
            'forms' => (object)array('erreur' => 'this is a error'),
            'fields' => (object)array('erreur' => ''),
            'value_lists' => (object)array('erreur' => ''),
            'contacts' => (object)array('erreur' => ''),
            'segments' => (object)array('erreur' => ''),
            );
        $param  = array('mperf' => $mperf,'apiError' => '');
        $this->assertTrue( utils::isNotNull(np6Utils::CheckApiError($param)));

    }

    //warning only work if you test the folder because class configuration not found if not
    public function testdeleteConfigurationFile()
    {
        $this->assertTrue(np6Utils::deleteConfigurationFile());
    }


}

