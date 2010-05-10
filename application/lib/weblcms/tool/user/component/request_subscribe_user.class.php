<?php
/**
 * $Id: request.class.php 224 2010-04-06 14:40:30Z Yannick $
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */

require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

class UserToolRequestSubscribeUserComponent extends UserToolComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $failures = 0;
                
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS)), Translation :: get('SubscribeUsers')));
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_REQUEST_SUBSCRIBE_USER)), Translation :: get('RequestSubscribeUser')));
        $trail->add_help('course request');
           
        $course = $this->get_course();
        $request = new CourseRequest();
        $form = new CourseRequestForm(CourseRequestForm :: TYPE_CREATE, $this->get_url(), $course, $this, $request);
       
        if($form->validate())
        {
			$success_request = $form->create_request();
        	$array_type = array();
	        $array_type['go'] = WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME;
            $this->redirect(Translation :: get($success_request ? 'RequestCreated' : 'RequestNotCreated'), ($success_request ? false : true), $array_type, array(WeblcmsManager :: PARAM_COURSE)); 	
        }
        else
        {
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('RequestForm')));
            $this->display_header();
            $form->display();
            $this->display_footer();
        }   
     }
}
?>