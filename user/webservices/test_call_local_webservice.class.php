<?php
/**
 * $Id: test_call_local_webservice.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.webservices
 */
require_once (dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
$time_start = microtime(true);
$handler = new TestCallLocalWebservice();

$handler->run();

class TestCallLocalWebservice
{
    private $webservice;

    function TestCallLocalWebservice()
    {
        $this->webservice = Webservice :: factory($this);
    }

    function run()
    {
        //TEST 1 :  Get User
        

        /*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.get_user',
                'parameters' => array('input' => array(User :: PROPERTY_USERNAME => 'SSoliber'),'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
		);*/
        
        //TEST 2 : Get User Courses
        

        /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.get_user_courses',
				'parameters' => array('id' => '4','hash'=>'550859312670dd7996153002d046737f08ba2c9f'),
		  		'handler' => 'handle_webservice'
			);*/
        
        //TEST 3 : Get Group
        

        /*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesUser.get_all_users',
				'parameters' => array('hash'=>'8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        //TEST 4 : Login Webservice
        //$wsdl = 'http://www.chamilo.org/demo_portal/user/webservices/login_webservice.class.php?wsdl';
        /*$wsdl = 'http://localhost/user/webservices/login_webservice.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'LoginWebservice.login',
				'parameters' => array('input' => array('username'=>'Soliber','password'=>'c14d68b0ef49d97929c36f7725842b5adbf5f006'), 'hash' =>''), //password is actually hash 1
				'handler' => 'handle_webservice'
			);
		}*/
        
        //TEST 5 :  Create User
        

        /*$user = array (
		  'lastname' => 'Jos',
		  'firstname' => 'Den Os',
		  'username' => 'Soliberr',
		  'password' => '4a0091108fb271e05f34da7cf77c975f',
		  'auth_source' => 'platform',
		  'email' => 'admin@localhost.localdomain',
		  'status' => '1',
		  'admin' => '1',
		  'phone' => NULL,
		  'official_code' => 'ADMIN',
		  'picture_uri' => NULL,
		  'creator_id' => 'Soliber',
		  'language' => 'english',
		  'disk_quota' => '209715200',
		  'database_quota' => '300',
		  'version_quota' => '20',
		  'theme' => NULL,
		  'activation_date' => '0',
		  'expiration_date' => '0',
		  'registration_date' => '1234774883',
		  'active' => '1'
        );

        //$wsdl = 'http://www.chamilo.org/demo_portal/user/webservices/webservices_user.class.php?wsdl';
        $wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
        $functions = array();
        $functions[] = array(
            'name' => 'WebServicesUser.create_user',
            'parameters' => array('input' => $user,'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
            'handler' => 'handle_webservice'
        );*/
        
        //TEST 6 :: Update User
        

        $user = array('lastname' => 'Oske', 'firstname' => 'Jos', 'username' => 'admin', 'password' => 'b9921b6ebaac9174f01ea9e2fe3df9f95010410b', 'auth_source' => 'platform', 'email' => 'admin@localhost.localdomain', 'status' => '1', 'admin' => '1', 'phone' => NULL, 'official_code' => 'ADMIN', 'picture_uri' => NULL, 'creator_id' => NULL, 'language' => 'english', 'disk_quota' => '209715200', 'database_quota' => '300', 'version_quota' => '20', 'theme' => NULL, 'activation_date' => '0', 'expiration_date' => '0', 'registration_date' => '1234774883', 'active' => '1');
        
        $wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
        $functions = array();
        
        $functions[] = array('name' => 'WebServicesUser.update_users', 'parameters' => array('input' => array($user), 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'), 'handler' => 'handle_webservice');
        
        //TEST 7 : Delete User
        

        /*$user = array (
		  'lastname' => 'Jos',
		  'firstname' => 'Den Os',
		  'username' => 'Zorro',
		  'password' => '4a0091108fb271e05f34da7cf77c975f',
		  'auth_source' => 'platform',
		  'email' => 'admin@localhost.localdomain',
		  'status' => '1',
		  'admin' => '1',
		  'phone' => NULL,
		  'official_code' => 'ADMIN',
		  'picture_uri' => NULL,
		  'creator_id' => 'Soliber',
		  'language' => 'english',
		  'disk_quota' => '209715200',
		  'database_quota' => '300',
		  'version_quota' => '20',
		  'theme' => NULL,
		  'activation_date' => '0',
		  'expiration_date' => '0',
		  'registration_date' => '1234774883',
		  'active' => '1',
        );
        
        $wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.delete_users',
				'parameters' => array('input' => array($user), 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
		);*/
        
        //TEST 8 : Create Course
        

        /*$course = array (

            'course_language' => 'english',
            'title' => 'Kennisintensieve Toepassingen',
            'description' => '',
            'category' 	=> 'Language skills', //needs the name, not the id
            'visibility' => '1',
            'show_score' => '1',
            'titular' => 'Stefaan', //needs the username, not the id
            'visual_code' => 'KIT',
            'department_name' => '',
            'department_url' => '',
            'disk_quota' => '200', //needs to > 1
            'target_course_code' => '',
            'layout' => '1',
            'subscribe' => '1',
            'unsubscribe' => '0',
            'theme' => '1',
            'tool_shortcut' => '1',
            'menu' 	=> '1',
            'breadcrumb' => '1',
            'allow_feedback' => '1',
            'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f' //hash 3 needed for credential
            );
            
          //$wsdl = 'http://www.chamilo.org/demo_portal/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.create_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/
        
        //TEST 9 : Update Course
        

        /*$course = array (
            'course_language' => 'english',
            'title' => 'Kennisintensieve Toepassingen',
            'category' 	=> 'Language skills', //needs the name, not the id
            'visibility' => '1',
            'show_score' => '1',
            'titular' => 'Stefaan', //needs the username, not the id
            'visual_code' => 'KIT',
            'department_name' => 'BINF',
            'department_url' => '',
            'disk_quota' => '200', //needs to > 1
            'target_course_code' => '',
            'layout' => '1',
            'subscribe' => '1',
            'unsubscribe' => '0',
            'theme' => '1',
            'tool_shortcut' => '1',
            'menu' 	=> '1',
            'breadcrumb' => '1',
            'allow_feedback' => '1',
            'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f' //hash 3 needed for credential
            );

          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.update_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/
        
        //TEST 10 : Delete Course
        

        /*$course = array (
            'course_language' => 'english',
            'title' => 'Kennisintensieve Toepassingen',
            'category' 	=> 'Language skills', //needs the name, not the id
            'visibility' => '1',
            'show_score' => '1',
            'titular' => 'Stefaan', //needs the username, not the id
            'visual_code' => 'KIT',
            'department_name' => 'BINF',
            'department_url' => '',
            'disk_quota' => '200', //needs to > 1
            'target_course_code' => '',
            'layout' => '1',
            'subscribe' => '1',
            'unsubscribe' => '0',
            'theme' => '1',
            'tool_shortcut' => '1',
            'menu' 	=> '1',
            'breadcrumb' => '1',
            'allow_feedback' => '1',
            'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f' //hash 3 needed for credential
            );

          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.delete_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/
        
        //TEST 11 : Subscribe User
        

        /*$course = array (
            'user_id' => 'Soliber', //expect name
            'tutor_id' => 'Stefaan',
            'status' => '1',
            'course_group_id' => '0',
            'course_code' => 'KIT', //the name is course_code, because we expect a course_user_rel, but the the value is visual_code
            'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f' //hash 3 needed for credential
            );

          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.subscribe_user',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/
        
        //TEST 12 : Unsubscribe User
        

        /*$course = array (
            'user_id' => 'Soliber', //expect name
            'tutor_id' => 'Stefaan',
            'status' => '1',
            'course_group_id' => '0',
            'course_code' => 'KIT', //the name is course_code, because we expect a course_user_rel, but the the value is visual_code
            'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f' //hash 3 needed for credential
            );

          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.unsubscribe_user',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/
        
        //TEST 13 :  Get all users
        

        /*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.get_all_users',
				'parameters' => array('hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
		);*/
        
        /*TEST 14 : A test to subscribe a group to a course in the db
		 *
		 */
        
        /* $coursegroup = array (
		  	  'course_code' => 'KIT',
			  'name' => 'testgroup',
			  'description' => 'test',
			  'max_number_of_members' => '999',
			  'self_reg_allowed' => '1',
			  'self_unreg_allowed' => '1',
              'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'
			);
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.subscribe_group',
				'parameters' => $coursegroup,
		  		'handler' => 'handle_webservice'
			);*/
        
        /*TEST 15 : A test to unsubscribe a group from a course in the db
		 *
		 */
        
        /*$coursegroup = array (
		  	  'course_code' => 'KIT',
			  'name' => 'testgroup',
			  'description' => 'test',
			  'max_number_of_members' => '999',
			  'self_reg_allowed' => '1',
			  'self_unreg_allowed' => '1',
              'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'
			);
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.unsubscribe_group',
				'parameters' => $coursegroup,
		  		'handler' => 'handle_webservice'
			);*/
        
        //TEST 14 : Get Group
        /*$wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesGroup.get_group',
				'parameters' => array('name' => 'SShinsengumi','hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        //TEST 15 : Delete Group
        /*$group = array (
			    'name' => 'ShinsengumiXXVII',
			    'description' => 'test',
			    'sort' => '1',
			    'parent' => '1',
                'hash' => '550859312670dd7996153002d046737f08ba2c9f'
			);

		  $wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesGroup.delete_group',
				'parameters' => $group,
		  		'handler' => 'handle_webservice'			
			);*/
        
        //TEST 16 :  Get all Users
        

        /*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.get_all_users',
				'parameters' => array('hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
		);*/
        
        //TEST 17 :  Create Users
        

        /* $users = array ('0' => array
        (
            'lastname' => 'Doe',
            'firstname' => 'Jane',
            'username' => 'Spiderman',
            'password' => 'ae12e345f679aaf',
            'auth_source' => 'platform',
            'email' => 'admin@localhost.localdomain',
            'status' => '1',
            'admin' => '',
            'phone' => '',
            'official_code' => 'ADMIN',
            'picture_uri' =>'',
            'creator_id' => 'Soliber',
            'language' => 'english',
            'disk_quota' => '209715200',
            'database_quota' => '300',
            'version_quota' => '20',
            'theme' => '',
            'activation_date' => '0',
            'expiration_date' => '0',
            'registration_date' => '1238155721',
            'active' => 1
        ),

    '1' => array
        (
            'lastname' => 'Doe',
            'firstname' => 'Joe',
            'username' => 'Spidermannnnn',
            'password' => 'ae12e345f679aaf',
            'auth_source' => 'platform',
            'email' => 'admin@localhost.localdomain',
            'status' => '1',
            'admin' => '',
            'phone' => '',
            'official_code' => 'ADMIN',
            'picture_uri' =>'',
            'creator_id' => 'Soliber',
            'language' => 'english',
            'disk_quota' => '209715200',
            'database_quota' => '300',
            'version_quota' => '20',
            'theme' => '',
            'activation_date' => '0',
            'expiration_date' => '0',
            'registration_date' => '1238155721',
            'active' => 1
        ),

      '2' => array
        (
            'lastname' => 'Doe',
            'firstname' => 'Johnson',
            'username' => 'Spidermannnntetnn',
            'password' => 'ae12e345f679aaf',
            'auth_source' => 'platform',
            'email' => 'admin@localhost.localdomain',
            'status' => '1',
            'admin' => '',
            'phone' => '',
            'official_code' => 'ADMIN',
            'picture_uri' =>'',
            'creator_id' => 'Soliber',
            'language' => 'english',
            'disk_quota' => '209715200',
            'database_quota' => '300',
            'version_quota' => '20',
            'theme' => '',
            'activation_date' => '0',
            'expiration_date' => '0',
            'registration_date' => '1238155721',
            'active' => 1,
        
        )
);

        //$wsdl = 'http://www.chamilo.org/demo_portal/user/webservices/webservices_user.class.php?wsdl';
        $wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
        $functions = array();
        $functions[] = array(
            'name' => 'WebServicesUser.create_users',
            'parameters' => array('input' => $users, 'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
            'handler' => 'handle_webservice'
        );*/
        
        //TEST 17 :  Delete Users
        

        /*$users = array ('0' => array
        (
            'lastname' => 'Doe',
            'firstname' => 'Jane',
            'username' => 'Spiderman',
            'password' => 'ae12e345f679aaf',
            'auth_source' => 'platform',
            'email' => 'admin@localhost.localdomain',
            'status' => '1',
            'admin' => '',
            'phone' => '',
            'official_code' => 'ADMIN',
            'picture_uri' =>'',
            'creator_id' => 'Soliber',
            'language' => 'english',
            'disk_quota' => '209715200',
            'database_quota' => '300',
            'version_quota' => '20',
            'theme' => '',
            'activation_date' => '0',
            'expiration_date' => '0',
            'registration_date' => '1238155721',
            'active' => 1
        ),

    '1' => array
        (
            'lastname' => 'Doe',
            'firstname' => 'Joe',
            'username' => 'Spidermannnnn',
            'password' => 'ae12e345f679aaf',
            'auth_source' => 'platform',
            'email' => 'admin@localhost.localdomain',
            'status' => '1',
            'admin' => '',
            'phone' => '',
            'official_code' => 'ADMIN',
            'picture_uri' =>'',
            'creator_id' => 'Soliber',
            'language' => 'english',
            'disk_quota' => '209715200',
            'database_quota' => '300',
            'version_quota' => '20',
            'theme' => '',
            'activation_date' => '0',
            'expiration_date' => '0',
            'registration_date' => '1238155721',
            'active' => 1
        ),

      '2' => array
        (
            'lastname' => 'Doe',
            'firstname' => 'Johnson',
            'username' => 'Spidermannnntetnn',
            'password' => 'ae12e345f679aaf',
            'auth_source' => 'platform',
            'email' => 'admin@localhost.localdomain',
            'status' => '1',
            'admin' => '',
            'phone' => '',
            'official_code' => 'ADMIN',
            'picture_uri' =>'',
            'creator_id' => 'Soliber',
            'language' => 'english',
            'disk_quota' => '209715200',
            'database_quota' => '300',
            'version_quota' => '20',
            'theme' => '',
            'activation_date' => '0',
            'expiration_date' => '0',
            'registration_date' => '1238155721',
            'active' => 1,

        )
);

        //$wsdl = 'http://www.chamilo.org/demo_portal/user/webservices/webservices_user.class.php?wsdl';
        $wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
        $functions = array();
        $functions[] = array(
            'name' => 'WebServicesUser.delete_users',
            'parameters' => array('input' => $users, 'hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
            'handler' => 'handle_webservice'
        );*/
        
        $this->webservice->call_webservice($wsdl, $functions);
    }

    function handle_webservice($result)
    {
        $time_end = microtime(true);
        $time = $time_end - $GLOBALS['time_start'];
        echo "Execution time was  $time seconds\n";
        //global $file;
        //fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");
        //echo ('<p>'.date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n".'</p>');
        //echo '<pre>'.var_export($result,true).'</pre>';
        dump($result);
    }
}

?>