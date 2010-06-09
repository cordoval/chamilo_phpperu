<?php

class SurveyManagerReportingComponent extends SurveyManager
{

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
        
        $classname = Request :: get(ReportingManager :: PARAM_TEMPLATE_NAME);
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $publication_id = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $publication = $this->retrieve_survey_publication($publication_id);
        $this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $publication_id);
        
        if ($publication->is_test())
        {
            $trail->add(new Breadcrumb($this->get_testcase_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
        }
        
        else
        {
            $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        }
        
        //        $strail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $this->pid)), Translation :: get('TakeSurvey')));
        

        $trail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_TEMPLATE_NAME => $classname, ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS => $params)), Translation :: get('Report')));
        $trail->add_help('survey reporting');
        
        $rtv = new ReportingViewer($this);
        $rtv->add_template_by_name('survey_publication_reporting_template', SurveyManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->show_all_blocks();
        
        $rtv->run();
    }
}
?>