<?php
require_once dirname(__FILE__) . '/../gradebook_manager_component.class.php';
require_once dirname(__FILE__) . '/../gradebook_manager.class.php';

class GradebookManagerAdminActiveChangerComponent extends GradebookManagerComponent
{
	function run()
	{
		$active = Request :: get(GradebookManager :: PARAM_ACTIVE);
		
	    if (! $this->get_user()->is_platform_admin())
        {
            $trail = new BreadcrumbTrail();
            $trail->add_help('course_type_active_changer');
            $this->display_header($trail);
            Display :: error_message(Translation :: get("Not allowed"));
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
	            $evaluation_format = $this->get_parent()->retrieve_evaluation_format($id);
	            
	            if ($active)
	            {
	            	$evaluation_format->set_active(1);
	            	$activation = 'activated';
	            }
	            else
	            {
	            	$evaluation_format->set_active(0);
	            	$activation = 'deactivated';
	            }
	            
	            if ($evaluation_format->update())
	            {
	                //Events :: trigger_event('update', 'evaluation_format', array('target_evaluation_format_id' => $evaluation_format->get_id(), 'action_evaluation_format_id' => $this->get_evaluation_format()->get_id()));
	            }
	            else
	            {
	            	$failures++;
	            }
			}
            
			if($activation == 'deactivated')
				$message = $this->get_result($failures, count($course_type_id), 'EvaluationFormatNotDeactivated' , 'EvaluationFormatsNotDeactivated', 'EvaluationFormatDeactivated', 'EvaluationFormatsDeactivated');
			else
				$message = $this->get_result($failures, count($course_type_id), 'EvaluationFormatNotActivated' , 'EvaluationFormatsNotActivated', 'EvaluationFormatActivated', 'EvaluationFormatsActivated');
			
            $this->redirect($message, ($failures > 0), array(Application :: PARAM_ACTION => GradebookManager:: ACTION_ADMIN_BROWSE_EVALUATION_FORMATS));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
	}	 
}
?>