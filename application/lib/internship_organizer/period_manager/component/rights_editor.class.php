<?php

class InternshipOrganizerPeriodManagerRightsEditorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $periods = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        
        $this->set_parameter(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID, $periods);
        
        if ($periods && ! is_array($periods))
        {
            $periods = array($periods);
        }
        
        $locations = array();
        
        foreach ($periods as $period_id)
        {
            
        	$period = InternshipOrganizerDataManager::get_instance()->retrieve_period($period_id);
        	if ($this->get_user()->is_platform_admin() || $period->get_owner() == $this->get_user_id())
            {
                $locations[] = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($period_id, InternshipOrganizerRights :: TYPE_PERIOD);
            }
        }
        
        $manager = new RightsEditorManager($this, $locations);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_available_rights()
    {
        
        return InternshipOrganizerRights :: get_available_rights_for_periods();
    
    }

}
?>