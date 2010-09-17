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
        $trail = BreadcrumbTrail :: get_instance();
        
        $organisation = new InternshipOrganizerOrganisation();
        $form = new InternshipOrganizerOrganisationForm(InternshipOrganizerOrganisationForm :: TYPE_CREATE, $organisation, $this->get_url(), $this->get_user());
      
        if ($form->validate())
        {
            $success = $form->create_organisation();
            if ($success)
            {
                $organisation = $form->get_organisation();
                $this->redirect(Translation :: get('InternshipOrganizerOrganisationCreated'), (false), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerOrganisationNotCreated'), (true), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_ORGANISATION));
            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    
    }
}
?>