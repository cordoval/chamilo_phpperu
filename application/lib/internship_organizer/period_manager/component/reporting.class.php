<?php

class InternshipOrganizerPeriodManagerReportingComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $period_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        $period = InternshipOrganizerDataManager :: get_instance()->retrieve_period($period_id);
        $this->set_parameter(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID, $period_id);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('period general');
        
        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name('internship_organizer_period_reporting_template', InternshipOrganizerManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->hide_all_blocks();
        
        $rtv->run();
    }
}
?>