<?php
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

/**
 * Component to edit an existing request object
 * @author Yannick Meert
 */
class WeblcmsManagerCourseRequestAllowComponent extends WeblcmsManager
{
	private $form;
	private $request_type;
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$request_ids = Request :: get(WeblcmsManager :: PARAM_REQUEST);
		$this->request_type = Request :: get(WeblcmsManager:: PARAM_REQUEST_TYPE);
		$failures = 0;
		
		if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }   

        $trail = BreadcrumbTrail :: get_instance();
        
         if ($this->get_user()->is_platform_admin())
         {
    		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
            $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER )), Translation :: get('Requests')));
        }
        else
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('CourseTypes')));       
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AllowRequest')));
        $trail->add_help('allow request');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
		 
		$request_method = null;
        
        switch($this->request_type)
        {
        	case CommonRequest :: SUBSCRIPTION_REQUEST: $request_method = 'retrieve_request'; break;
        	case CommonRequest :: CREATION_REQUEST: $request_method = 'retrieve_course_create_request'; break;
        }
        		
		$request = $this->$request_method($request_ids[0]);	
			
		$this->form = new CourseRequestForm(CourseRequestForm :: TYPE_EDIT, $this->get_url(array(WeblcmsManager :: PARAM_REQUEST => $request_ids, WeblcmsManager :: PARAM_REQUEST_TYPE => $this->request_type)), $course, $this, $request, $this->get_user_id());
		
		if($this->form->validate())
		{
			if(!is_null($request_ids) && $this->get_user()->is_platform_admin())
			{
				if(! is_array($request_ids))
				{
					$request_ids = array($request_ids);
				}
			
				foreach($request_ids as $request_id)
				{				
					if(!$this->update_date($request_id))
					{
						$failures ++;
					}
        		}
				if ($failures)
            	{
                	if (count($request_ids) == 1)
                	{
                    	$message = 'SelectedRequestNotAllowed';
                	}
                	else
                	{
                    	$message = 'SelectedRequestsNotAllowed';
                	}
            	}
            	else
            	{
                	if (count($request_ids) == 1)
                	{
                    	$message = 'SelectedRequestAllowed';
                	}
                	else
                	{
                    	$message = 'SelectedRequestsAllowed';
                	}
            	}
            	$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER, WeblcmsManager :: PARAM_REQUEST => null));
			}
			else
        	{
            	$this->display_error_page(htmlentities(Translation :: get('NoRequestsSelected')));
        	}
		}
	 	else
        {
            $this->display_header();
            $this->form->display();
            $this->display_footer();
        }
	}
	function update_date($request_id)
    {
    	$request_method = null;
        
        switch($this->request_type)
        {
        	case CommonRequest :: SUBSCRIPTION_REQUEST: $request_method = 'retrieve_request'; break;
        	case CommonRequest :: CREATION_REQUEST: $request_method = 'retrieve_course_create_request'; break;
        }
        				
        $new_date = $this->form->get_selected_date_decision();       	
        $wdm = WeblcmsDataManager :: get_instance();
        $request = $wdm->$request_method($request_id);
        $request->set_decision_date($new_date);
        $request->set_decision(CommonRequest :: ALLOWED_DECISION);
        return $request->update($request);
    }
}
?>