<?php
/**
 * $Id: test_call_local_webservice.class.php 215 2009-11-13 14:07:59Z vanpouckesven $ 
 * @package reporting.webservices
 * @author Michael Kyndt
 */
require_once (dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';

/*// No PHP-memory limits
ini_set("memory_limit", "3500M"	);
// Two hours should be enough
ini_set("max_execution_time", "7200");
*/
$handler = new TestCallLocalWebservice();
/*
$start_total = microtime(true);
$file = fopen(dirname(__FILE__) . 'test.txt', 'w');
*/
$handler->run();
/*
$stop_total = microtime(true);
$time = $stop_total - $start_total;
fwrite($file, 'Total: ' . $time . ' s');
fclose($file);
*/
class TestCallLocalWebservice
{
    private $webservice;

    function TestCallLocalWebservice()
    {
        $this->webservice = Webservice :: factory($this);
    }

    function run()
    {
        /*A test to retrieve courses of a user from the db
		 * 
		 */
        
        /*$wsdl = 'http://localhost/reporting/webservices/webservices_reporting.class.php?wsdl';
        $functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesReporting.get_user_courses',
				'parameters' => array('input' => array('username' => 'JohnDoe'),'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to retrieve users of a course from the db
		 * 
		 */
        
        /*$wsdl = 'http://localhost/reporting/webservices/webservices_reporting.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesReporting.get_course_users',
				'parameters' => array('input' => array('visual_code' => 'KIT'),'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to get new publications in course X from the db
		 * 
		 */
        
        /*$wsdl = 'http://localhost/reporting/webservices/webservices_reporting.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesReporting.get_new_publications_in_course',
				'parameters' => array('input' => array('user_id' => 'admin', 'course_code' => 'KIT'), 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to get new publications in course X, tool Y from the db
		 *
		 */
        
        /*$wsdl = 'http://localhost/reporting/webservices/webservices_reporting.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesReporting.get_new_publications_in_course_tool',
                'parameters' => array('input' => array('user_id' => 'admin', 'course_code' => 'KIT', 'tool' => 'wiki'), 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to get publications for user X from the db
		 *
		 */
        
        /*$wsdl = 'http://localhost/reporting/webservices/webservices_reporting.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesReporting.get_publications_for_user',
				'parameters' => array('input' => array('username' => 'admin'), 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to get publications for course X from the db
		 *
		 */
        
        $wsdl = 'http://localhost/reporting/webservices/webservices_reporting.class.php?wsdl';
        $functions = array();
        
        {
            $functions[] = array('name' => 'WebServicesReporting.get_publications_for_course', 'parameters' => array('input' => array('visual_code' => 'KIT'), 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'), 'handler' => 'handle_webservice');
        }
        
        $this->webservice->call_webservice($wsdl, $functions);
    }

    function handle_webservice($result)
    {
        //global $file;
        //fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");
        //echo ('<p>'.date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n".'</p>');
        //echo '<pre>'.var_export($result,true).'</pre';
        dump($result);
    }
}

?>