<?php
/**
 * $Id: test_call_local_webservice.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.webservices
 */
require_once (dirname(__FILE__) . '/../../../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../course/course.class.php';

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
        /*A test to retrieve a course from the db
		 * 
		 */
        
        /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_course',
				'parameters' => array('input' => array('visual_code' => 'KIT'),'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to retrieve courses of a user from the db
		 * 
		 */
        
        /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
        $functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_user_courses',
				'parameters' => array('input' => array('username' => 'JohnDoe'),'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to retrieve users of a course from the db
		 * 
		 */
        
        /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_course_users',
				'parameters' => array('input' => array(Course :: PROPERTY_VISUAL => 'COBOL'),'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to get new publications in course X from the db
		 * 
		 */
        
        /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_new_publications_in_course',
				'parameters' => array('user_id' => 2, 'id' => 3, 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to get new publications in course X, tool Y from the db
		 *
		 */
        
        /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_new_publications_in_course_tool',
				'parameters' => array('user_id' => 2, 'id' => 3, 'tool' => 'announcement', 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to get publications for user X from the db
		 *
		 */
        
        /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_publications_for_user',
				'parameters' => array('id' => 1, 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to get publications for course X from the db
		 *
		 */
        
        /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_publications_for_course',
				'parameters' => array('id' => 1, 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        /*A test to delete a course in the db
		 * 
		 */
        
        /*$course = array(
            'layout' => '1',
            'visual_code' => 'KITT',
            'category' => 'language skills', //required
            'title' => 'Kennisintensieve Toepassingen', //required
            'show_score' => '1',
            'titular' => 'Soliber',
            'course_language' => 'english',
            'department_url' => '' ,
            'department_name' => '',
            'visibility' => '1',
            'subscribe' => '1',
            'unsubscribe' => '1',
            'theme' => '1',
            'tool_shortcut' => '1',
            'menu' => '1',
            'breadcrumb' => '1',
            'allow_feedback' => '1',
            'disk_quota' => '200',
        );

		$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.delete_courses',
				'parameters' => array('input' => array($course),'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
		  		'handler' => 'handle_webservice'			
			);*/
        
        /*A test to create a course in the db
		 * 
		 */
        
        $course = array('layout' => '1', 'vissual_code' => 'KITT', 'category' => 'language skills', //required
'title' => 'Kennisintensieve Toepassingennnn', //required
'show_score' => '1', 'titular' => 'Soliber', 'course_language' => 'english', 'department_url' => '', 'department_name' => '', 'visibility' => '1', 'subscribe' => '1', 'unsubscribe' => '1', 'theme' => '1', 'tool_shortcut' => '1', 'menu' => '1', 'breadcrumb' => '1', 'allow_feedback' => '1', 'disk_quota' => '200');
        
        $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
        $functions = array();
        $functions[] = array('name' => 'WebServicesCourse.create_course', 'parameters' => array('input' => $course, 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'), 'handler' => 'handle_webservice');
        
        /*A test to subscribe a user to a course in the db
		 * 
		 */
        
        /*$courses = array(
              array (
			  'course_code' => 'KIT',
			  'user_id' => 'Soliber',
			  'status' => '1',
			  'role' => 'NULL',
			  'course_group_id' => 'testgroup',
			  'tutor_id' => 'Soliber',
			  'sort' => '0',
			  'user_course_cat' => '0'
        ),
        array (
			  'course_code' => 'KIT',
			  'user_id' => 'admin',
			  'status' => '1',
			  'role' => 'NULL',
			  'course_group_id' => 'testgroup',
			  'tutor_id' => 'Soliber',
			  'sort' => '0',
			  'user_course_cat' => '0'
        )
    );

		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.subscribe_users',
				'parameters' => array('input' => $courses,'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
		  		'handler' => 'handle_webservice'			
			);*/
        
        /*A test to unsubscribe a user from a course in the db
		 * 
		 */
        
        /*$courses = array(
              array (
			  'course_code' => 'KIT',
			  'user_id' => 'Soliber',
			  'status' => '1',
			  'role' => 'NULL',
			  'course_group_id' => 'testgroup',
			  'tutor_id' => 'Soliber',
			  'sort' => '0',
			  'user_course_cat' => '0'
        ),
        array (
			  'course_code' => 'KIT',
			  'user_id' => 'admin',
			  'status' => '1',
			  'role' => 'NULL',
			  'course_group_id' => 'testgroup',
			  'tutor_id' => 'Soliber',
			  'sort' => '0',
			  'user_course_cat' => '0'
        )
    );
	   	  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.unsubscribe_users',
				'parameters' => array('input' => $courses,'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
		  		'handler' => 'handle_webservice'			
			);*/
        
        /*A test to subscribe a group to a course in the db
		 * 
		 */
        
        /*$coursegroups = array(
             array (
		  	  'course_code' => 'KIT',
			  'name' => 'test',
			  'description' => 'test',
			  'max_number_of_members' => '999',
			  'self_reg_allowed' => '1',
			  'self_unreg_allowed' => '1'
            ),
             array (
		  	  'course_code' => 'KIT',
			  'name' => 'testertest',
			  'description' => 'test',
			  'max_number_of_members' => '999',
			  'self_reg_allowed' => '1',
			  'self_unreg_allowed' => '1'
            )
        );
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.subscribe_groups',
				'parameters' => array('input' => $coursegroups,'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
		  		'handler' => 'handle_webservice'			
			);*/
        
        /*A test to unsubscribe a group from a course in the db
		 * 
		 */
        
        /*$coursegroups = array(
             array (
		  	  'course_code' => 'KIT',
			  'name' => 'test',
			  'description' => 'test',
			  'max_number_of_members' => '999',
			  'self_reg_allowed' => '1',
			  'self_unreg_allowed' => '1'
            ),
             array (
		  	  'course_code' => 'KIT',
			  'name' => 'testertest',
			  'description' => 'test',
			  'max_number_of_members' => '999',
			  'self_reg_allowed' => '1',
			  'self_unreg_allowed' => '1'
            )
        );
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.unsubscribe_groups',
				'parameters' => array('input' => $coursegroups,'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
		  		'handler' => 'handle_webservice'			
			);*/
        
        /*A test to update a course in the db
		 * 
		 */
        
        /*$course = array(
            'layout' => '1',
            'visual_code' => 'KITT',
            'category' => 'language skills', //required
            'title' => 'Kennisintensieve Toepassingen', //required
            'show_score' => '1',
            'titular' => 'Soliber',
            'course_language' => 'english',
            'department_url' => '' ,
            'department_name' => '',
            'visibility' => '1',
            'subscribe' => '1',
            'unsubscribe' => '1',
            'theme' => '1',
            'tool_shortcut' => '1',
            'menu' => '1',
            'breadcrumb' => '1',
            'allow_feedback' => '1',
            'disk_quota' => '200',
        );
		
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.update_courses',
				'parameters' => array('input' => array($course),'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
		  		'handler' => 'handle_webservice'			
			);*/
        
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