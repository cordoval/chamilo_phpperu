<?php
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

/**
 * Component to edit an existing request object
 * @author Yannick Meert
 */
class WeblcmsManagerCourseRequestAllowComponent extends WeblcmsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$request_id = Request :: get(WeblcmsManager :: PARAM_REQUEST);
		if(!is_null($request_id) && $this->get_user()->is_platform_admin())
		{
			$request = $this->retrieve_request($request_id);
			$request->set_allowed_date(Utilities :: to_db_date(time()));
			
			if(!$request->update())
			{
				$success_request = false;
			}
			else
			{
				$success_request = true;
			}
        }
        else
        {
        	$success_request = false;
        }
        
        $array_type = array();
	   	$array_type['go'] = WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER;
        $this->redirect(Translation :: get($success_request ? 'RequestUpdated' : 'RequestNotUpdated'), ($success_request ? false : true), $array_type);			
	}
}
?>