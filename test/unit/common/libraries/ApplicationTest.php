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


