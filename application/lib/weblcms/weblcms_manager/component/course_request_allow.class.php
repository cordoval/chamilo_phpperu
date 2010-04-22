<?php
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

/**
 * Component to edit an existing request object
 * @author Yannick Meert
 */
class WeblcmsManagerCourseRequestAllowComponent extends WeblcmsManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$request_ids = Request :: get(WeblcmsManager :: PARAM_REQUEST);
		$failures = 0;
		
		if(!is_null($request_ids) && $this->get_user()->is_platform_admin())
		{
			if(! is_array($request_ids))
			{
				$request_ids = array($request_ids);
			}
			
			foreach($request_ids as $request_id)
			{
				$request = $this->retrieve_request($request_id);
				$request->set_allowed_date(Utilities :: to_db_date(time()));
			
				if(!$request->update())
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
}
?>