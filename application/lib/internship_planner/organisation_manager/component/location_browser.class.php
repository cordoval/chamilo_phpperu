<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/forms/location_form.class.php';

class InternshipOrganisationManagerLocationBrowserComponent extends InternshipOrganisationnManagerComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail();
       
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseInternshipLocations')));
        
        $this->display_header($trail);
        
        echo '<a href="' . $this->get_create_location_url() . '">' . Translation :: get('CreateInternshipLocation') . '</a>';
        echo '<br /><br />';
        
        $locations = $this->retrieve_locations();
        while ($location = $locations->next_result())
        {
            echo '<div style="border: 1px solid grey; padding: 5px;">';
            dump($location);
            echo '<br /><a href="' . $this->get_update_location_url($location) . '">' . Translation :: get('UpdateInternshipLocation') . '</a>';
            echo ' | <a href="' . $this->get_delete_location_url($location) . '">' . Translation :: get('DeleteInternshipLocation') . '</a>';
            echo '</div><br /><br />';
        }
        
        $this->display_footer();
    }

}
?>