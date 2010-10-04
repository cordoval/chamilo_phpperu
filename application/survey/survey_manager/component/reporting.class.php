<?php

class SurveyManagerReportingComponent extends SurveyManager implements DelegateComponent
{

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

        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication_id);
        $this->set_parameter(self :: PARAM_PUBLICATION_ID, $publication_id);

	    $breadcrumbtrail = BreadcrumbTrail::get_instance();
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveyPublications')));
        

        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name('survey_publication_reporting_template', self :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($breadcrumbtrail);
        $rtv->show_all_blocks();

        $rtv->run();
    }
}
?>