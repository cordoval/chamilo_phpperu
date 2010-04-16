<?php
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

/**
 * Component to edit an existing request object
 * @author Yannick Meert
 */
class WeblcmsManagerCourseRequestEditorComponent extends WeblcmsManagerComponent
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
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateRequest')));		
		
		$request = $this->retrieve_request($request_id);
		$form = new CourseRequestForm(CourseRequestForm :: TYPE_EDIT, $this->get_url(array(WeblcmsManager :: PARAM_REQUEST => $request->get_id())), $course, $this, $request, $this->get_user());
		
		if($form->validate())
		{
			$success_request = $form->update_request();
			$array_type = array();
	        $array_type['go'] = WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER;
	        
            $this->redirect(Translation :: get($success_request ? 'RequestUpdated' : 'RequestNotUpdated'), ($success_request ? false : true), $array_type);	}
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