<?php
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

/**
 * Component to edit an existing request object
 * @author Yannick Meert
 */
class WeblcmsManagerCourseRequestRefuseComponent extends WeblcmsManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$request_ids = Request :: get(WeblcmsManager :: PARAM_REQUEST);
		$request_type = Request :: get(WeblcmsManager:: PARAM_REQUEST_TYPE);
		$failures = 0;
		
		if(!is_null($request_ids) && $this->get_user()->is_platform_admin())
		{
			if(! is_array($request_ids))
			{
				$request_ids = array($request_ids);
			}
			
			foreach($request_ids as $request_id)
			{
				$request_method = null;
        
        		switch($request_type)
        		{
        			case CommonRequest :: SUBSCRIPTION_REQUEST: $request_method = 'retrieve_request'; break;
        			case CommonRequest :: CREATION_REQUEST: $request_method = 'retrieve_course_create_request'; break;
        		}
        		
				$request = $this->$request_method($request_id);				
				$request->set_decision_date(time());
				$request->set_decision(CommonRequest::DENIED_DECISION);
							
				if(!$request->update())
				{
					$failures ++;
				}
        	}
			if ($failures)
            {
                if (count($request_ids) == 1)
                {
                    $message = 'SelectedRequestNotDenied';
                }
                else
                {
                    $message = 'SelectedRequestsNotDenied';
                }
            }
            else
            {
                if (count($request_ids) == 1)
                {
                    $message = 'SelectedRequestDenied';
                }
                else
                {
                    $message = 'SelectedRequestsDenied';
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