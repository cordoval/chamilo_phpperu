<?php
require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';

class ToolToolEvaluateComponent extends ToolComponent
{
    function run()
    {
        if (Request :: get('parameters'))
        {
        	$parameter_string = Request :: get('parameters');
        	$parameters = unserialize(base64_decode($parameter_string));
        }
        if ($parameters[EvaluationManager :: PARAM_PUBLICATION_ID])
        {
        	
        	$tool_publication = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication($parameters[EvaluationManager :: PARAM_PUBLICATION_ID]);
        	$trail = new BreadcrumbTrail();
        	$trail->add(new Breadcrumb('', Translation :: get('Wiki')));
    		$trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE, EvaluationManager :: PARAM_PARAMETERS => $parameter_string)), Translation :: get('Evaluations') . ' ' . $tool_publication->get_content_object()->get_title()));
			$evaluation_manager = new EvaluationManager($this, $tool_publication, Request :: get('action'), $trail);
        }  
        else
        {
            $this->display_error_message(Translation :: get('NoPublicationSelected'));
        }
    }    
}
?>