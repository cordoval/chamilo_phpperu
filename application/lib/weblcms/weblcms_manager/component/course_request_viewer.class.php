<?php
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

/**
 * Component to view an existing request object
 * @author Yannick Meert
 */
class WeblcmsManagerCourseRequestViewerComponent extends WeblcmsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$request_id = Request :: get(WeblcmsManager :: PARAM_REQUEST);
		
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER)), Translation :: get('Courses')));
        $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER)), Translation :: get('Request')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('View Request')));		
		
		$request = $this->retrieve_request($request_id);
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
			$this->display_header($trail, false, true);
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