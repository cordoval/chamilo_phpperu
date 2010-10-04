<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/period_form.class.php';

class InternshipOrganizerPeriodManagerEditorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
              
        $id = Request :: get(self :: PARAM_PERIOD_ID);
        if ($id)
        {
            $period = $this->retrieve_period($id);
            
            $form = new InternshipOrganizerPeriodForm(InternshipOrganizerPeriodForm :: TYPE_EDIT, $period, $this->get_period_editing_url($period), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_period();
                $period = $form->get_period();
                $this->redirect(Translation :: get($success ? 'InternshipOrganizerPeriodUpdated' : 'InternshipOrganizerPeriodNotUpdated'), ($success ? false : true), array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => $period->get_id()));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerPeriodSelected')));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS)), Translation :: get('BrowseInternshipOrganizerPeriods')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PERIOD_ID);
    }

}
?>