<?php
require_once dirname(__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';

class ToolComponentToolEvaluateComponent extends ToolComponent implements EvaluationManagerInterface
{
    private $publication_id;
    private $publisher_id;

    function run()
    {
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $tool_publication = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
            $this->publication_id = $tool_publication->get_id();
            $this->publisher_id = $tool_publication->get_publisher_id();
            
            BreadcrumbTrail :: get_instance()->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE)), Translation :: get('BrowseEvaluations') . ' ' . $tool_publication->get_content_object()->get_title()));
            $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $this->publication_id);
            
            EvaluationManager :: launch($this);
        }
        else
        {
            $this->display_error_message(Translation :: get('NoPublicationSelected'));
        }
    }

    function get_publication_id()
    {
        return $this->publication_id;
    }

    function get_publisher_id()
    {
        return $this->publisher_id;
    }
}
?>