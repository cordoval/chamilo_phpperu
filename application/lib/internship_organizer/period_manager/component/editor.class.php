<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/period_form.class.php';

class InternshipOrganizerPeriodManagerEditorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('period general');
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        $trail->add(new Breadcrumb($this->get_browse_periods_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));
        
        $id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        if ($id)
        {
            $period = $this->retrieve_period($id);
            $trail->add(new Breadcrumb($this->get_period_viewing_url($period), $period->get_name()));
            $trail->add(new Breadcrumb($this->get_period_editing_url($period), Translation :: get('UpdateInternshipOrganizerPeriod') . ' ' . $period->get_name()));
            
            $form = new InternshipOrganizerPeriodForm(InternshipOrganizerPeriodForm :: TYPE_EDIT, $period, $this->get_period_editing_url($period), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_period();
                $period = $form->get_period();
                $this->redirect(Translation :: get($success ? 'InternshipOrganizerPeriodUpdated' : 'InternshipOrganizerPeriodNotUpdated'), ($success ? false : true), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period->get_id()));
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
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerPeriodSelected')));
        }
    }
}
?>