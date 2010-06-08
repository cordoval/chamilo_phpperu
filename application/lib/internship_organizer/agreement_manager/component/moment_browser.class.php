<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/moment_form.class.php';

class InternshipOrganizerAgreementManagerMomentBrowserComponent extends InternshipOrganizerAgreementManager
{

    function run()
    {
        $trail = new BreadcrumbTrail();
       
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseInternshipOrganizerMoments')));
        
        $this->display_header($trail);
        
        echo '<a href="' . $this->get_create_moment_url() . '">' . Translation :: get('CreateInternshipOrganizerMoment') . '</a>';
        echo '<br /><br />';
        
        $moments = $this->retrieve_moments();
        while ($moment = $moments->next_result())
        {
            echo '<div style="border: 1px solid grey; padding: 5px;">';
            echo '<br /><a href="' . $this->get_update_moment_url($moment) . '">' . Translation :: get('UpdateInternshipOrganizerMoment') . '</a>';
            echo ' | <a href="' . $this->get_delete_moment_url($moment) . '">' . Translation :: get('DeleteInternshipOrganizerMoment') . '</a>';
            echo '</div><br /><br />';
        }
        
        $this->display_footer();
    }

}
?>