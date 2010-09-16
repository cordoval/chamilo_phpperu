<?php

class InternshipOrganizerAgreementManagerPublicationRightsEditorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $publications = Request :: get(InternshipOrganizerAgreementManager :: PARAM_PUBLICATION_ID);
        
        $this->set_parameter(InternshipOrganizerAgreementManager :: PARAM_PUBLICATION_ID, $publications);
        
        if ($publications && ! is_array($publications))
        {
            $publications = array($publications);
        }
        
        $locations = array();
        
        foreach ($publications as $publication_id)
        {
            
            $publication = InternshipOrganizerDataManager :: get_instance()->retrieve_publication($publication_id);
            if ($this->get_user()->is_platform_admin() || $publication->get_publisher_id() == $this->get_user_id())
            {
                $locations[] = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($publication_id, InternshipOrganizerRights :: TYPE_PUBLICATION);
            }
        }
        
        $publication_place = $publication->get_publication_place();
        
        $user_ids = array();
        
        switch ($publication_place)
        {
            case InternshipOrganizerPublicationPlace :: MOMENT :
                $moment = InternshipOrganizerDataManager :: get_instance()->retrieve_moment($publication->get_place_id());
                $agreement = $moment->get_agreement();
                $user_ids = $agreement->get_user_ids(InternshipOrganizerUserType :: get_user_types());
                break;
            case InternshipOrganizerPublicationPlace :: AGREEMENT :
                $agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($publication->get_place_id());
                $user_ids = $agreement->get_user_ids(InternshipOrganizerUserType :: get_user_types());
                break;
            case InternshipOrganizerPublicationPlace :: PERIOD :
                $period = InternshipOrganizerDataManager :: get_instance()->retrieve_period($publication->get_place_id());
                $user_ids = $period->get_user_ids(InternshipOrganizerUserType :: get_user_types());
                break;
            default :
                ;
                break;
        }
        
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
        
        return InternshipOrganizerRights :: get_available_rights_for_publications();
    
    }

}
?>