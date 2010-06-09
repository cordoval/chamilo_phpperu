<?php

class SurveyManagerQuestionReportingComponent extends SurveyManager
{
	
	const TEMPLATE_SURVEY_QUESTION_REPORTING = 'survey_question_reporting_template';
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        
    	if (! SurveyRights :: is_allowed(SurveyRights :: VIEW_RIGHT, 'reporting', 'sts_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
       
        $question_id = Request :: get(SurveyManager :: PARAM_SURVEY_QUESTION);
        $this->set_parameter(SurveyManager :: PARAM_SURVEY_QUESTION, $question_id);

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        $trail->add_help('survey reporting');
        
        $rtv = new ReportingViewer($this);
        $rtv->add_template_by_name(self :: TEMPLATE_SURVEY_QUESTION_REPORTING, SurveyManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->show_all_blocks();
        
        $rtv->run();
    }
}
?>