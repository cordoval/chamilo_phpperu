<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/location_form.class.php';

class InternshipOrganizerOrganisationManagerLocationUpdaterComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $location = $this->retrieve_location(Request :: get(InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID));
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_view_organisation_url($location->get_organisation()), Translation :: get('ViewOrganisation')));
        $trail->add(new Breadcrumb($this->get_update_location_url($location), Translation :: get('UpdateLocation')));
        
        $form = new InternshipOrganizerLocationForm(InternshipOrganizerLocationForm :: TYPE_EDIT, $location, $this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID => $location->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_location();
            $this->redirect($success ? Translation :: get('InternshipOrganizerLocationUpdated') : Translation :: get('InternshipOrganizerLocationNotUpdated'), ! $success, array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $location->get_organisation_id()));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>