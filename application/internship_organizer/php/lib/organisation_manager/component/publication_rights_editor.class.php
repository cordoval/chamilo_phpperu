<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_manager/component/browser.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_manager/component/viewer.class.php';

class InternshipOrganizerOrganisationManagerPublicationRightsEditorComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $publications = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        $this->set_parameter(self :: PARAM_PUBLICATION_ID, $publications);
        
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
                $location = $moment->get_location();
                $user_ids = $location->get_user_ids(InternshipOrganizerUserType :: get_user_types());
                break;
            case InternshipOrganizerPublicationPlace :: AGREEMENT :
                $agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($publication->get_place_id());
                $user_ids = $agreement->get_user_ids(InternshipOrganizerUserType :: get_user_types());
                break;
            case InternshipOrganizerPublicationPlace :: PERIOD :
                $period = InternshipOrganizerDataManager :: get_instance()->retrieve_period($publication->get_place_id());
                $user_ids = $period->get_user_ids(InternshipOrganizerUserType :: get_user_types());
                break;
            case InternshipOrganizerPublicationPlace :: LOCATION :
                $location = InternshipOrganizerDataManager :: get_instance()->retrieve_location($publication->get_place_id());
                $organisation = $location->get_organisation();
                $user_ids = $organisation->get_user_ids();
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerOrganisation')));
        
        $location_id = Request :: get(self :: PARAM_LOCATION_ID);
        if ($location_id)
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_LOCATION, self :: PARAM_LOCATION_ID => $location_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerLocationViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerLocation')));
        }
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID, self :: PARAM_LOCATION_ID);
    }

}
?>