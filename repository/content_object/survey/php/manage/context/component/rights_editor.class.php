<?php

class SurveyContextManagerRightsEditorComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
    	$component_id = Request :: get(self :: PARAM_COMPONENT_ID);
    	
        if (! SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: RIGHT_VIEW, $component_id, SurveyContextManagerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        
        $location = SurveyContextManagerRights :: get_location_by_identifier_from_survey_context_manager_subtree($component_id, SurveyContextManagerRights :: TYPE_COMPONENT);
             
        $manager = new RightsEditorManager($this, array($location));
        $manager->run();
    }

    function get_available_rights()
    {
        return SurveyContextManagerRights :: get_available_rights_for_components();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
//        $component_id = Request :: get(self :: PARAM_COMPONENT_ID);
//        $location = SurveyContextManagerRights :: get_location_by_identifier_from_survey_context_manager_subtree($component_id, SurveyContextManagerRights :: TYPE_COMPONENT);
//    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMINISTRATION, self :: PARAM_COMPONENT_ID => Request :: get(self :: PARAM_COMPONENT_ID))), Translation :: get($location->get_location())));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_COMPONENT_ID);
    }
}
?>