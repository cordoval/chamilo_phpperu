<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/location_form.class.php';

class InternshipOrganizerOrganisationManagerLocationEditorComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $location = $this->retrieve_location(Request :: get(InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID));
        
        $form = new InternshipOrganizerLocationForm(InternshipOrganizerLocationForm :: TYPE_EDIT, $location, $this->get_url(array(self :: PARAM_LOCATION_ID => $location->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_location();
            $this->redirect($success ? Translation :: get('InternshipOrganizerLocationUpdated') : Translation :: get('InternshipOrganizerLocationNotUpdated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $location->get_organisation_id()));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
}
?>