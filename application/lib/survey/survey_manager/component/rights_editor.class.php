<?php

class SurveyManagerRightsEditorComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        //have to be fixed in reighteditormanager, if array is set as paramater, rightsmenu don't work anymore
        

        $publications = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        //        if ($publications && ! is_array($publications))
        //        {
        //            $publications = array($publications);
        //        }
        

        $this->set_parameter(SurveyManager :: PARAM_PUBLICATION_ID, $publications);
        
        if ($publications && ! is_array($publications))
        {
            $publications = array($publications);
        }
        
        $locations = array();
        
        foreach ($publications as $publication)
        {
            
            $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication);
            if ($this->get_user()->is_platform_admin() || $pub->get_publisher() == $this->get_user_id())
            {
                $locations[] = SurveyRights :: get_location_by_identifier_from_surveys_subtree($publication, SurveyRights :: TYPE_PUBLICATION);
            }
        }
        
        $manager = new RightsEditorManager($this, $locations);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_available_rights()
    {
        
        return SurveyRights :: get_available_rights_for_publications();
    
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }

}
?>