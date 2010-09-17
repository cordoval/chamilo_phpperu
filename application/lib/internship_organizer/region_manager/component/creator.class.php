<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/region_form.class.php';

class InternshipOrganizerRegionManagerCreatorComponent extends InternshipOrganizerRegionManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $region_id = Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID);
        $trail->add_help('region general');
        
        $region = new InternshipOrganizerRegion();
        $region->set_parent_id($region_id);
        $form = new InternshipOrganizerRegionForm(InternshipOrganizerRegionForm :: TYPE_CREATE, $region, $this->get_url(array(InternshipOrganizerRegionManager :: PARAM_REGION_ID => $region_id)), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_region();
            if ($success)
            {
                $region = $form->get_region();
                $this->redirect(Translation :: get('InternshipOrganizerRegionCreated'), (false), array(InternshipOrganizerRegionManager :: PARAM_ACTION => InternshipOrganizerRegionManager :: ACTION_BROWSE_REGIONS, InternshipOrganizerRegionManager :: PARAM_REGION_ID => $region->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerRegionNotCreated'), (true), array(InternshipOrganizerRegionManager :: PARAM_ACTION => InternshipOrganizerRegionManager :: ACTION_BROWSE_REGIONS, InternshipOrganizerRegionManager :: PARAM_REGION_ID => $region_id));
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