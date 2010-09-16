<?php

class InternshipOrganizerAgreementManagerMomentRightsEditorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $moments = Request :: get(InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID);
        
        $this->set_parameter(InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID, $moments);
        
        if ($moments && ! is_array($moments))
        {
            $moments = array($moments);
        }
        
        $locations = array();
        
        foreach ($moments as $moment_id)
        {
            
        	$moment = InternshipOrganizerDataManager::get_instance()->retrieve_moment($moment_id);
        	if ($this->get_user()->is_platform_admin() || $moment->get_owner() == $this->get_user_id())
            {
                $locations[] = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($moment_id, InternshipOrganizerRights :: TYPE_MOMENT);
            }
        }
        
        $agreement = $moment->get_agreement();
        
        $manager = new RightsEditorManager($this, $locations);
        $user_ids = $agreement->get_user_ids(InternshipOrganizerUserType::get_user_types());
                
        if(count($user_ids) > 0){
        	 $manager->limit_users($user_ids);
        }else{
        	$manager->limit_users(array(0));
        }
       
        $manager->set_modus(RightsEditorManager :: MODUS_USERS);
        $manager->run();
    }

    function get_available_rights()
    {
        
        return InternshipOrganizerRights :: get_available_rights_for_moments();
    
    }

}
?>