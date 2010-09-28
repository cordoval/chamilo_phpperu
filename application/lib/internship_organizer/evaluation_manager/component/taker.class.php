<?php

require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/display/component/viewer/survey_viewer_wizard.class.php';


class InternshipOrganizerEvaluationManagerTakerComponent extends InternshipOrganizerEvaluationManager
{
    
	private $survey_id;
    private $publication_id;
    private $invitee_id;

    function run()
    {
        
        $this->survey_id = Request :: get(SurveyViewerWizard :: PARAM_SURVEY_ID);
        
        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
         
        $this->invitee_id = Request :: get(SurveyViewerWizard :: PARAM_INVITEE_ID);
               
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PARTICIPATE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION, $this->user_id))
        {
            Display :: not_allowed();
        }
        
        ComplexDisplay :: launch(Survey :: get_type_name(), $this, false);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseEvaluations')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID, SurveyViewerWizard :: PARAM_SURVEY_ID, SurveyViewerWizard :: PARAM_INVITEE_ID, SurveyViewerWizard :: PARAM_CONTEXT_PATH);
    }

    //try out for interface SurveyTaker


    function started($survey_id)
    {

    }

    function finish($survey_id)
    {
    
    }

    function started_context($survey_id, $context_template, $context_id)
    {
        

       
    }

    function finish_context($survey, $template_id, $context_id)
    {
    
    }

    function save_answers($question_id, $answer, $template_id, $context_id)
    {

    }

}

?>