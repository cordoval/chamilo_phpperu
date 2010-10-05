<?php
require_once Path :: get_application_path() . 'internship_organizer/php/organisation_manager/component/viewer.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/forms/location_form.class.php';

class InternshipOrganizerOrganisationManagerLocationCreatorComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $organisation_id = $_GET[self :: PARAM_ORGANISATION_ID];
        
        $location = new InternshipOrganizerLocation();
        $location->set_organisation_id($organisation_id);
        
        $form = new InternshipOrganizerLocationForm(InternshipOrganizerLocationForm :: TYPE_CREATE, $location, $this->get_url(array(self :: PARAM_ORGANISATION_ID => $organisation_id)), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_location();
            $this->redirect($success ? Translation :: get('InternshipOrganizerLocationCreated') : Translation :: get('InternshipOrganizerLocationNotCreated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation_id));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_LOCATIONS)), Translation :: get('ViewInternshipOrganizerOrganisation')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID);
    }
}
?>