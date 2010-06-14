<?php
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

/**
 * Component to view an existing request object
 * @author Yannick Meert
 */
class WeblcmsManagerCourseRequestViewerComponent extends WeblcmsManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }   

        $trail = BreadcrumbTrail :: get_instance();
        
         if ($this->get_user()->is_platform_admin())
         {
    		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
            $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER )), Translation :: get('Requests')));
        }
        else
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('CourseTypes')));       
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Print-ViewRequest')));
        $trail->add_help('update request');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        } 		
        
		$request_id = Request :: get(WeblcmsManager :: PARAM_REQUEST);
		$request_type = Request :: get(WeblcmsManager:: PARAM_REQUEST_TYPE);
		
        $request_method = null;
        
        switch($request_type)
        {
        	case CommonRequest :: SUBSCRIPTION_REQUEST: $request_method = 'retrieve_request'; break;
        	case CommonRequest :: CREATION_REQUEST: $request_method = 'retrieve_course_create_request'; break;
        }
        		
		$request = $this->$request_method($request_id);
		$form = new CourseRequestForm(CourseRequestForm :: TYPE_VIEW, $this->get_url(array(WeblcmsManager :: PARAM_REQUEST => $request->get_id())), $course, $this, $request, $this->get_user());
		
		if($form->validate())
		{
			//$success_request = $form->print_request();
			$array_type = array();
	        $array_type['go'] = WeblcmsManager :: ACTION_VIEW_REQUEST;
	        
            //$this->redirect(Translation :: get($success_request ? 'RequestView' : 'RequestNoView'), ($success_request ? false : true), $array_type);
            $this->simple_redirect(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER));
		}
		else
		{
			$this->display_header();
			$form->display();
			
		}
		$this->display_footer();
	}
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}
}
?>