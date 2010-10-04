<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/organisation_form.class.php';

class InternshipOrganizerOrganisationManagerCreatorComponent extends InternshipOrganizerOrganisationManager
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
        
        $organisation = new InternshipOrganizerOrganisation();
        $form = new InternshipOrganizerOrganisationForm(InternshipOrganizerOrganisationForm :: TYPE_CREATE, $organisation, $this->get_url(), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_organisation();
            if ($success)
            {
                $organisation = $form->get_organisation();
                $this->redirect(Translation :: get('InternshipOrganizerOrganisationCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerOrganisationNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION));
            }
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
    }

}
?>