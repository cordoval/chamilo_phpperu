<?php

require_once Path :: get_application_path() . 'lib/survey/survey_publication_rel_reporting_template_registration.class.php';

class SurveyReportingManagerReportingComponent extends SurveyReportingManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $publication_rel_template_registration_id = Request :: get(self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID);
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_VIEW, $publication_rel_template_registration_id, SurveyRights :: TYPE_REPORTING_TEMPLATE_REGISTRATION))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $condition = new EqualityCondition(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_ID, $publication_rel_template_registration_id);
        $publication_rel_template_registration = SurveyDataManager :: get_instance()->retrieve_survey_publication_rel_reporting_template_registrations($condition, 0, 1)->next_result();
		 
        
        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_id($publication_rel_template_registration->get_reporting_template_registration_id());
        $rtv->hide_all_blocks();
        $rtv->run();
  

    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE, SurveyManager :: PARAM_PUBLICATION_ID => Request :: get(SurveyManager :: PARAM_PUBLICATION_ID))), Translation :: get('BrowseReportingTemplates')));
    
    }

    function get_additional_parameters()
    {
        return array(SurveyManager :: PARAM_PUBLICATION_ID, self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID, self :: PARAM_CONTEXT_TEMPLATE_ID);
    }
	    
}
?>