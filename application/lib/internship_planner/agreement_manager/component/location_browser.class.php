<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/forms/moment_form.class.php';

class InternshipPlannerAgreementManagerMomentBrowserComponent extends InternshipPlannerAgreementnManagerComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail();
       
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseInternshipPlannerMoments')));
        
        $this->display_header($trail);
        
        echo '<a href="' . $this->get_create_moment_url() . '">' . Translation :: get('CreateInternshipPlannerMoment') . '</a>';
        echo '<br /><br />';
        
        $moments = $this->retrieve_moments();
        while ($moment = $moments->next_result())
        {
            echo '<div style="border: 1px solid grey; padding: 5px;">';
            echo '<br /><a href="' . $this->get_update_moment_url($moment) . '">' . Translation :: get('UpdateInternshipPlannerMoment') . '</a>';
            echo ' | <a href="' . $this->get_delete_moment_url($moment) . '">' . Translation :: get('DeleteInternshipPlannerMoment') . '</a>';
            echo '</div><br /><br />';
        }
        
        $this->display_footer();
    }

}
?>