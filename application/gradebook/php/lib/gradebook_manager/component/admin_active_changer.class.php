<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/gradebook_manager.class.php';

class GradebookManagerAdminActiveChangerComponent extends GradebookManager
{
	function run()
	{
		$active = Request :: get(GradebookManager :: PARAM_ACTIVE);

	    if (! $this->get_user()->is_platform_admin())
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add_help('course_type_active_changer');
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $evaluation_format_id = Request :: get(GradebookManager :: PARAM_EVALUATION_FORMAT_ID);
        if(!is_array($evaluation_format_id))
        {
        	$evaluation_format_id = array($evaluation_format_id);
        }
        
        if (count($evaluation_format_id) > 0)
        {
        	$failures = 0;
        	$activation = '';
			foreach($evaluation_format_id as $id)
			{
	            $evaluation_format = $this->retrieve_evaluation_format($id);
				if(!isset($active))
				{
					if ($evaluation_format->get_active()== 1)
						$active = 0;
					else
						$active = 1;
				}
	            
	            $evaluation_format->set_active($active);
	            if (!$evaluation_format->update())
	            {
	            	$failures++;
	            }
			}
            
			if($active == 0)
				$message = $this->get_result($failures, count($evaluation_format_id), 'EvaluationFormatNotDeactivated' , 'EvaluationFormatsNotDeactivated', 'EvaluationFormatDeactivated', 'EvaluationFormatsDeactivated');
			else
				$message = $this->get_result($failures, count($evaluation_format_id), 'EvaluationFormatNotActivated' , 'EvaluationFormatsNotActivated', 'EvaluationFormatActivated', 'EvaluationFormatsActivated');
			
            $this->redirect($message, ($failures > 0), array(Application :: PARAM_ACTION => GradebookManager:: ACTION_ADMIN_BROWSE_EVALUATION_FORMATS));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
	}	 
}
?>