<?php

require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/survey_publication_browser/survey_publication_browser_table.class.php';

class SurveyManagerBuilderComponent extends SurveyManager
{
    private $content_object;

    function run()
    {
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication_id);
        $this->set_parameter(SurveyManager :: PARAM_PUBLICATION_ID, $publication_id);
        $this->content_object = $publication->get_publication_object();
    
        ComplexBuilder :: launch($this->content_object->get_type(), $this, false);
     
    }
    
	function get_root_content_object()
    {
    	return $this->content_object;
    }

}
?>