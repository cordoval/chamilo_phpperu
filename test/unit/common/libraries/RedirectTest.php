<?php
namespace common\libraries;

class RedirectTest extends \PHPUnit_Framework_TestCase{


    private $request;
    private $queryString;
    private $self;

    protected function setUp()
    {
        $this->self = '/chamilo/sub/path/ressource.extension';
        $this->queryString = 'key1=value1&key2=value2';
        $this->request = $this->self . '?' . $this->queryString;
        $_SERVER['PHP_SELF'] = $this->self;
        $_SERVER['QUERY_STRING'] = $this->queryString;

    }

    public function test_get_url_should_be_consistent_with_php_self_and_chop_query_string()
    {
        $return_value = Redirect :: get_url();
        $this->assertEquals($this->self, $return_value);
    }

    public function test_get_url_filters_parameter_based_on_arguments()
    {        
        $parameters = array(
            'greets' => 'Hi', 
            'bad word' => '******'
        );
        $filters = array(
            'bad word'
        );

        $return_value = Redirect :: get_url($parameters, $filters);
        $this->assertEquals($this->self . '?greets=Hi', $return_value);
    }
}

