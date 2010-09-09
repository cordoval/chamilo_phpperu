<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/period_subscribe_user_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/browser.class.php';


class InternshipOrganizerPeriodManagerSubscribeUserComponent extends InternshipOrganizerPeriodManager
{
    private $period;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
//        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
//        //$trail->add(new Breadcrumb($this->get_browse_periods_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));
        
        $period_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        $this->period = $this->retrieve_period($period_id);
        
//        //$trail->add(new Breadcrumb($this->get_period_viewing_url($this->period), $this->period->get_name()));
//        //$trail->add(new Breadcrumb($this->get_period_subscribe_user_url($this->period), Translation :: get('AddInternshipOrganizerUsers')));
        $trail->add_help('period subscribe users');
        
        $form = new InternshipOrganizerPeriodSubscribeUserForm($this->period, $this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_period_rel_users();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodRelUsersCreated'), (false), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $this->period->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_USERS));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodRelUsersNotCreated'), (true), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $this->period->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_USERS));
            }
        }
        else
        {
            $this->display_header($trail, true);
            $form->display();
            $this->display_footer();
        }
    }

    function get_period()
    {
        return $this->period;
    }

}
?>