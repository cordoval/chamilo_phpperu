<?php

class InternshipOrganizerPeriodManagerReportingComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $period_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        $period = InternshipOrganizerDataManager::get_instance()->retrieve_period($period_id);
        $this->set_parameter(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID, $period_id);

        $trail = BreadcrumbTrail :: get_instance();
//        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
//        //$trail->add(new Breadcrumb($this->get_browse_periods_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));
        $trail->add_help('period general');

        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name('internship_organizer_period_reporting_template', InternshipOrganizerManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->hide_all_blocks();

        $rtv->run();
    }
}
?>