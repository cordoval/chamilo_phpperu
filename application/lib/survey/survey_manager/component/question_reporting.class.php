<?php

class SurveyManagerQuestionReportingComponent extends SurveyManager implements DelegateComponent
{
    
    const TEMPLATE_SURVEY_QUESTION_REPORTING = 'survey_question_reporting_template';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_VIEW, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_SURVEY_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $question_id = Request :: get(self :: PARAM_SURVEY_QUESTION_ID);
        $this->set_parameter(self :: PARAM_SURVEY_QUESTION_ID, $question_id);
       
        $breadcrumbtrail = BreadcrumbTrail :: get_instance();
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PAGES, self ::PARAM_SURVEY_ID => Request :: get(self :: PARAM_SURVEY_ID))), Translation :: get('BrowseSurveyPages')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PAGE_QUESTIONS, self ::PARAM_SURVEY_PAGE_ID => Request :: get(self :: PARAM_SURVEY_PAGE_ID))), Translation :: get('BrowseSurveyPageQuestions')));
    	
        
        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name(self :: TEMPLATE_SURVEY_QUESTION_REPORTING, SurveyManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($breadcrumbtrail);
        $rtv->show_all_blocks();
        
        $rtv->run();
    }
    
    
    
}
?>