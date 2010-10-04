<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/region_form.class.php';

class InternshipOrganizerRegionManagerCreatorComponent extends InternshipOrganizerRegionManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
      
        $region_id = Request :: get(self :: PARAM_REGION_ID);
      
        $region = new InternshipOrganizerRegion();
        $region->set_parent_id($region_id);
        $form = new InternshipOrganizerRegionForm(InternshipOrganizerRegionForm :: TYPE_CREATE, $region, $this->get_url(array(self :: PARAM_REGION_ID => $region_id)), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_region();
            if ($success)
            {
                $region = $form->get_region();
                $this->redirect(Translation :: get('InternshipOrganizerRegionCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS, self :: PARAM_REGION_ID => $region->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerRegionNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS, self :: PARAM_REGION_ID => $region_id));
            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS)), Translation :: get('BrowseInternshipOrganizerRegions')));
    }
	
    
}
?>