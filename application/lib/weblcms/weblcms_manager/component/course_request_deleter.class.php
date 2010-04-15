<?php
/**
 * $Id: course_request_deleter.class.php 218 2010-03-15 10:30:26Z Yannick $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
/**
 * Repository manager component which provides functionality to delete a course_type
 */
class WeblcmsManagerCourseRequestDeleterComponent extends WeblcmsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $request_ids = Request :: get(WeblcmsManager :: PARAM_REQUEST);
        $failures = 0;
        
        if (! $this->get_user()->is_platform_admin())
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('DeleteRequest')));
            $trail->add_help('request delete');
            
            
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_header($trail, false, true);
            $this->display_footer();
            exit();
        }
        
        if (! empty($request_ids))
        {
        	$wdm = WeblcmsDataManager::get_instance();
            if (! is_array($request_ids))
            {
                $request_ids = array($request_ids);
            }
            
            foreach ($request_ids as $request_id)
            {                
            	$request = $this->get_parent()->retrieve_request($request_id);
            	
            	if (! $request->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($request_id) == 1)
                {
                    $message = 'SelectedRequestNotDeleted';
                }
                else
                {
                    $message = 'SelectedRequestsNotDeleted';
                }
            }
            else
            {
                if (count($request_id) == 1)
                {
                    $message = 'SelectedRequestDeleted';
                }
                else
                {
                    $message = 'SelectedRequestsDeleted';
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