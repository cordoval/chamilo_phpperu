<?php

class SurveyManagerRightsEditorComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
	//have to be fixed in reighteditormanager, if array is set as paramater, rightsmenu don't work anymore
    	
        $publications = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        
//        if ($publications && ! is_array($publications))
//        {
//            $publications = array($publications);
//        }
        
        $this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $publications);
        
                if ($publications && ! is_array($publications))
                {
                    $publications = array($publications);
                }
                
        $locations = array();
        
        foreach ($publications as $publication)
        {
            if ($this->get_user()->is_platform_admin() || $publication->get_publisher() == $this->get_user_id())
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

}
?>