<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/period_form.class.php';


class InternshipOrganizerPeriodManagerCreatorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $period_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        $trail->add(new Breadcrumb($this->get_url(array( InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS , InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period_id)), Translation :: get('BrowseInternshipOrganizerPeriods')));
        $trail->add(new Breadcrumb($this->get_period_create_url, Translation :: get('CreateInternshipOrganizerPeriod')));
        $trail->add_help('period general');
             
        $period = new InternshipOrganizerPeriod();
        $period->set_parent_id(Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID));
        $form = new InternshipOrganizerPeriodForm(InternshipOrganizerPeriodForm :: TYPE_CREATE, $period, $this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_period();
            if ($success)
            {
                $period = $form->get_period();
                $this->redirect(Translation :: get('InternshipOrganizerPeriodCreated'), (false), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodNotCreated'), (true), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS));
            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }
}
?>