<?php
namespace common\libraries;

class ApplicationTest extends \PHPUnit_Framework_TestCase{

    private $application_instance;

    private $user_stub;

    protected function setUp() {
        parent::setUp();
        stubs\TableHandlerStub :: $table_has_been_handled = false;

        
        $this->user_stub = $this->getMock('user\\User');
        $this->application_instance = $this->getMockForAbstractClass(
                'common\\libraries\\Application',
                array($this->user_stub));
    }



    public function test_handle_table_action_should_set_parameters_and_call_callback_function()
    {
        $_POST['table_name'] = 'TableHandlerStub';
        $_POST['TableHandlerStub_namespace'] = 'common\\libraries\\stubs';
        $_POST['TableHandlerStub_action_name'] = 'action_name';
        $_POST['TableHandlerStub_action_value'] = 'action_value';

        $this->application_instance->handle_table_action();

        $this->assertTrue(stubs\TableHandlerStub::$table_has_been_handled);
        $this->assertEquals('action_value', Request :: get('action_name'));
        $this->assertEquals('action_value', $this->application_instance->get_parameter('action_name'));

    }

    public function test_get_url_should_build_a_query_string_based_on_parameters()
    {

        $_SERVER['PHP_SELF'] = '/path/to/executed/script';
        $this->application_instance->set_parameter('param1', 'value1');
        $this->application_instance->set_parameter('param2', 'value2');

        $additional_params = array(
            'param3' => 'value3',
            'param4' => 'value4'
        );

        $filters = array(
            'param3'
        );

        $return_value = $this->application_instance->get_url($additional_params, $filters);

        $this->assertEquals(
            '/path/to/executed/script?param1=value1&param2=value2&param4=value4',
            $return_value);
    }

    public function test_factory_should_be_able_to_construct_admin_core_app()
    {
        $return_value = $this->application_instance->factory('admin');        
        $this->assertInstanceOf('admin\AdminManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_group_core_app()
    {
        $return_value = $this->application_instance->factory('group');
        $this->assertInstanceOf('group\GroupManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_help_core_app()
    {
        $return_value = $this->application_instance->factory('help');
        $this->assertInstanceOf('help\HelpManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_home_core_app()
    {
        $return_value = $this->application_instance->factory('home');
        $this->assertInstanceOf('home\HomeManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_install_core_app()
    {
        $return_value = $this->application_instance->factory('install');
        $this->assertInstanceOf('install\InstallManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_menu_core_app()
    {
        $return_value = $this->application_instance->factory('menu');
        $this->assertInstanceOf('menu\MenuManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_reporting_core_app()
    {
        $return_value = $this->application_instance->factory('reporting');
        $this->assertInstanceOf('reporting\ReportingManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_rights_core_app()
    {
        $return_value = $this->application_instance->factory('rights');
        $this->assertInstanceOf('rights\RightsManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_tracking_core_app()
    {
        $return_value = $this->application_instance->factory('tracking');
        $this->assertInstanceOf('tracking\TrackingManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_user_core_app()
    {
        $return_value = $this->application_instance->factory('user');
        $this->assertInstanceOf('user\UserManager', $return_value);
    }
    public function test_factory_should_be_able_to_construct_webservice_core_app()
    {
        $return_value = $this->application_instance->factory('webservice');
        $this->assertInstanceOf('webservice\WebserviceManager', $return_value);
    }

    public function test_factory_should_be_unable_to_construct_unknown_app()
    {
        $this->setExpectedException('RuntimeException');
        $return_value = $this->application_instance->factory('unknown');
    }

    public function test_accessing_parameters_should_be_consistent_without_side_effect()
    {
        $params = array(
            'first' => 1,
            'second' => 2,
        );

        $this->assertEmpty($this->application_instance->get_parameters());
        $this->application_instance->set_parameters($params);
        $this->application_instance->set_parameter('third', 3);
        $this->application_instance->set_parameter('first', 'newone');
        $this->assertEquals(
            array(
                'first' => 'newone',
                'second' => 2,
                'third' => 3,
            ),
            $this->application_instance->get_parameters()
        );
        $this->assertEquals(
                array(
                    'first' => 1,
                    'second' => 2,
                ), $params
        );
               
    }
    
    public function test_display_portal_header()
    {
        $platformSettingsMock = $this->getMock('common\\libraries\\PlatformSetting');
        PlatformSetting::set_instance($platformSettingsMock);
        \ob_start();
	$this->application_instance->display_portal_header();
        $output = \ob_get_flush();
        $this->assertRegExp(
            '%<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">%is', 
            $output
        );
        $this->assertRegExp(
            '%<html.*?>.*?<head>.*?<title>.*?</title>.*?</head>.*?<body .*?>%is',
             $output
        );
    }


}


namespace common\libraries\stubs;

class TableHandlerStub
{
    public static $table_has_been_handled = false;

    public static function handle_table_action()
    {
        static :: $table_has_been_handled = true;
    }
}


