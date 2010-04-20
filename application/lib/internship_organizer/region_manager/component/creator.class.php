<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/region_form.class.php';


class InternshipOrganizerRegionManagerCreatorComponent extends InternshipOrganizerRegionManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_browse_regions_url(), Translation :: get('BrowseCategories')));
        
        $trail->add(new Breadcrumb($this->get_region_create_url, Translation :: get('CreateRegion')));
        $trail->add_help('region general');
             
        $region = new InternshipOrganizerRegion();
        $region->set_parent_id(Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID));
        $form = new InternshipOrganizerRegionForm(InternshipOrganizerRegionForm :: TYPE_CREATE, $region, $this->get_url(array(InternshipOrganizerRegionManager :: PARAM_REGION_ID => Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_region();
            if ($success)
            {
                $region = $form->get_region();
                $this->redirect(Translation :: get('InternshipOrganizerRegionCreated'), (false), array(InternshipOrganizerRegionManager :: PARAM_ACTION => InternshipOrganizerRegionManager :: ACTION_VIEW_REGION, InternshipOrganizerRegionManager :: PARAM_REGION_ID => $region->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerRegionNotCreated'), (true), array(InternshipOrganizerRegionManager :: PARAM_ACTION => InternshipOrganizerRegionManager :: ACTION_BROWSE_CATEGORIES));
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