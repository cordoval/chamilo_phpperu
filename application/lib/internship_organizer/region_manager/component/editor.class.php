<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/region_form.class.php';

class InternshipOrganizerRegionManagerEditorComponent extends InternshipOrganizerRegionManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('region general');
        $trail->add(new Breadcrumb($this->get_browse_regions_url(), Translation :: get('BrowseInternshipOrganizerRegions')));
        
        $id = Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID);
        if ($id)
        {
            $region = $this->retrieve_region($id);
            $trail->add(new Breadcrumb($this->get_region_viewing_url($region), $region->get_city_name()));
            $trail->add(new Breadcrumb($this->get_region_editing_url($region), Translation :: get('UpdateInternshipOrganizerRegion').' '.$region->get_city_name()));
                                  
            $form = new InternshipOrganizerRegionForm(InternshipOrganizerRegionForm :: TYPE_EDIT, $region, $this->get_region_editing_url($region), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_region();
                $region = $form->get_region();
                $this->redirect(Translation :: get($success ? 'InternshipOrganizerRegionUpdated' : 'InternshipOrganizerRegionNotUpdated'), ($success ? false : true), array(InternshipOrganizerRegionManager :: PARAM_ACTION => InternshipOrganizerRegionManager :: ACTION_VIEW_REGION, InternshipOrganizerRegionManager :: PARAM_REGION_ID => $region->get_id(), InternshipOrganizerRegionManager :: PARAM_PARENT_REGION_ID => $region->get_parent_id()));
            }
            else
            {
                $this->display_header($trail, false);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerRegionSelected')));
        }
    }
}
?>