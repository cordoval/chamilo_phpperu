<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/region_form.class.php';

class InternshipOrganizerRegionManagerEditorComponent extends InternshipOrganizerRegionManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
             
        $id = Request :: get(self :: PARAM_REGION_ID);
        if ($id)
        {
            
            $this->set_parameter(self :: PARAM_REGION_ID, $id);
            
            $region = $this->retrieve_region($id);
            
            $form = new InternshipOrganizerRegionForm(InternshipOrganizerRegionForm :: TYPE_EDIT, $region, $this->get_region_editing_url($region), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_region();
                $region = $form->get_region();
                $this->redirect(Translation :: get($success ? 'InternshipOrganizerRegionUpdated' : 'InternshipOrganizerRegionNotUpdated'), ($success ? false : true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS, self :: PARAM_REGION_ID => $region->get_id()));
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS)), Translation :: get('BrowseInternshipOrganizerRegions')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_REGION_ID);
    }

}
?>