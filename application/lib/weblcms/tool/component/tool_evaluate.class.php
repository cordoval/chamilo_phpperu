<?php
require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';

class ToolToolEvaluateComponent extends ToolComponent
{
    function run()
    {
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
        	$tool_publication = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
        	$publication_id = $tool_publication->get_id();
        	$publisher_id = $tool_publication->get_publisher_id();
        	$trail = new BreadcrumbTrail();
    		$trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE)), Translation :: get('BrowseEvaluations') . ' ' . $tool_publication->get_content_object()->get_title()));
			$this->set_parameter(Tool ::  PARAM_PUBLICATION_ID, $publication_id);
    		$evaluation_manager = new EvaluationManager($this, $publication_id, $publisher_id, Request :: get(EvaluationManager :: PARAM_EVALUATION_ACTION), $trail);
    		$evaluation_manager->run();
        }  
        else
        {
            $this->display_error_message(Translation :: get('NoPublicationSelected'));
        }
    }    
}
?>