<?php
/**
 * @package common.webservice.test
 */
require_once (dirname(__FILE__) . '/../../global.inc.php');
require_once dirname(__FILE__) . '/../webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_user.class.php';
require_once dirname(__FILE__) . '/provider/output_user.class.php';

$handler = new TestProvideWebserviceHandler();
$handler->run();

class TestProvideWebserviceHandler
{
    private $webservice;
    private $functions;

    function TestProvideWebserviceHandler()
    {
        $this->webservice = Webservice :: factory($this);
    }

    function run()
    {
        $functions = array();
        
        /*$functions['get_user'] = array(
			'input' => new InputUser(),
			'output' => new OutputUser()
		);*/
        
        //$this->webservice->provide_webservice($functions);
        

        $this->webservice->provide_webservice_with_wsdl(dirname(__FILE__) . "/wsdl.xml");
    }

    function get_user($input_user, $hash)
    {
        //$webservice->validate($hash);
        $user = new OutputUser();
        $user->set_name('Developer');
        $user->set_email('developer@chamilo.org');
        $user->set_gender('M');
        
        return $user->to_array();
    }
}