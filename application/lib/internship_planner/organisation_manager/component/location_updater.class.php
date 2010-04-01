<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/forms/location_form.class.php';

class InternshipPlannerOrganisationManagerLocationUpdaterComponent extends InternshipPlannerOrganisationManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $location = $this->retrieve_location(Request :: get(InternshipPlannerOrganisationManager :: PARAM_LOCATION_ID));
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_view_organisation_url($location->get_organisation()), Translation :: get('ViewOrganisation')));
        $trail->add(new Breadcrumb($this->get_update_location_url($location), Translation :: get('UpdateLocation')));
        
        $form = new InternshipPlannerLocationForm(InternshipPlannerLocationForm :: TYPE_EDIT, $location, $this->get_url(array(InternshipPlannerOrganisationManager :: PARAM_LOCATION_ID => $location->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_location();
            $this->redirect($success ? Translation :: get('InternshipPlannerLocationUpdated') : Translation :: get('InternshipPlannerLocationNotUpdated'), ! $success, array(InternshipPlannerOrganisationManager :: PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipPlannerOrganisationManager :: PARAM_ORGANISATION_ID => $location->get_organisation_id()));
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