<?php
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/viewer.class.php';


class InternshipOrganizerPeriodManagerPublicationRightsEditorComponent extends InternshipOrganizerPeriodManager
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), Translation :: get('BrowseInternshipOrganizerPeriods')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerPeriod')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PERIOD_ID);
    }

}
?>