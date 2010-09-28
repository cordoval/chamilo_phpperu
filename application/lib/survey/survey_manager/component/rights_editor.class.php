<?php

class SurveyManagerRightsEditorComponent extends SurveyManager implements DelegateComponent
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
        
        foreach ($publications as $publication_id)
        {
            
            $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication_id);
            $user_ids = array();
            if ($this->get_user()->is_platform_admin() || $publication->get_publisher() == $this->get_user_id())
            {
                $locations[] = SurveyRights :: get_location_by_identifier_from_surveys_subtree($publication_id, SurveyRights :: TYPE_PUBLICATION);
                
                $rights = SurveyRights :: get_available_rights_for_publications();
                foreach ($rights as $right)
                {
                    $publication_user_ids = SurveyRights :: get_allowed_users($right, $publication_id, SurveyRights :: TYPE_PUBLICATION);
                    $user_ids = array_merge($user_ids, $publication_user_ids);
                }
            }
        }
        
        $user_ids = array_unique($user_ids);
        
        $manager = new RightsEditorManager($this, $locations);
        
        if (count($user_ids) > 0)
        {
            $manager->limit_users($user_ids);
        }
        else
        {
            $manager->limit_users(array(0));
        }
        
        $manager->set_modus(RightsEditorManager :: MODUS_USERS);
        $manager->run();
    }

    function get_available_rights()
    {
        
        return SurveyRights :: get_available_rights_for_publications();
    
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }

}
?>