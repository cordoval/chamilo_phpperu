<?php

class InternshipOrganizerManagerRightsEditorComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_ADMINISTRATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $component_id = Request :: get(self :: PARAM_COMPONENT_ID);
        $location = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($component_id, InternshipOrganizerRights :: TYPE_COMPONENT);
     
        $manager = new RightsEditorManager($this, array($location));
        $manager->run();
    }

    function get_available_rights()
    {
        return InternshipOrganizerRights :: get_available_rights_for_components();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $component_id = Request :: get(self :: PARAM_COMPONENT_ID);
        $location = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($component_id, InternshipOrganizerRights :: TYPE_COMPONENT);
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMINISTRATION, self :: PARAM_COMPONENT_ID => Request :: get(self :: PARAM_COMPONENT_ID))), Translation :: get($location->get_location())));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_COMPONENT_ID);
    }
}
?>