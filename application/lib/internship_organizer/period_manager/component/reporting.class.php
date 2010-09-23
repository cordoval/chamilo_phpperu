<?php

class InternshipOrganizerPeriodManagerReportingComponent extends InternshipOrganizerPeriodManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $period_id = Request :: get(self :: PARAM_PERIOD_ID);
        $period = InternshipOrganizerDataManager :: get_instance()->retrieve_period($period_id);
        $this->set_parameter(self :: PARAM_PERIOD_ID, $period_id);
		
        $breadcrumbtrail = BreadcrumbTrail::get_instance();
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS)), Translation :: get('BrowseInternshipOrganizerPeriods')));
        
        
        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name('internship_organizer_period_reporting_template', InternshipOrganizerManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($breadcrumbtrail);
        $rtv->hide_all_blocks();
        
        $rtv->run();
    }
}
?>