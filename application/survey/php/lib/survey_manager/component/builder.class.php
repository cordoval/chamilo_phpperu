<?php namespace application\survey;

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