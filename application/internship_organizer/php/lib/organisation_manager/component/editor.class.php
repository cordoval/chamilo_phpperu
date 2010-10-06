<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/organisation_form.class.php';

class InternshipOrganizerOrganisationManagerEditorComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $organisation = $this->retrieve_organisation(Request :: get(self :: PARAM_ORGANISATION_ID));
        $form = new InternshipOrganizerOrganisationForm(InternshipOrganizerOrganisationForm :: TYPE_EDIT, $organisation, $this->get_url(array(self :: PARAM_ORGANISATION_ID => $organisation->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_organisation();
            $this->redirect($success ? Translation :: get('InternshipOrganizerOrganisationUpdated') : Translation :: get('InternshipOrganizerOrganisationNotUpdated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID))), Translation :: get('ViewInternshipOrganizerOrganisation')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID);
    }

}
?>