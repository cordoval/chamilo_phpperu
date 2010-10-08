<?php
/**
 * @package common.webservice.test
 */
require_once (dirname(__FILE__) . '/../../global.inc.php');
require_once dirname(__FILE__) . '/../webservice.class.php';
// No PHP-memory limits
ini_set("memory_limit", "3500M");
// Two hours should be enough
ini_set("max_execution_time", "7200");

$handler = new TestCallLocalWebservice();

$start_total = microtime(true);
$file = fopen(dirname(__FILE__) . 'test.txt', 'w');

$handler->run();

$stop_total = microtime(true);
$time = $stop_total - $start_total;
fwrite($file, 'Total: ' . $time . ' s');
fclose($file);

class TestCallLocalWebservice
{
    private $webservice;

    function TestCallLocalWebservice()
    {
        $this->webservice = Webservice :: factory($this);
    }

    function run()
    {
        $wsdl = 'http://localhost/lcms/common/webservices/test/test_provide_webservice_handler.class.php?wsdl';
        $functions = array();
        
        for($i = 0; $i < 10; $i ++)
        {
            $functions[] = array('name' => 'TestProvideWebserviceHandler.get_user', 'parameters' => array('id' => 1), 'handler' => 'handle_webservice');
        }
        
        $this->webservice->call_webservice($wsdl, $functions);
    }

    function handle_webservice($result)
    {
        global $file;
        fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");
    }
}

?>