<?php

require_once Path :: get_application_path() . 'internship_organizer/php/forms/period_subscribe_group_form.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/period_manager/component/browser.class.php';

class InternshipOrganizerPeriodManagerSubscribeGroupComponent extends InternshipOrganizerPeriodManager
{
   
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $period_id = Request :: get(self :: PARAM_PERIOD_ID);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_GROUP_RIGHT, $period_id, InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $period = $this->retrieve_period($period_id);
        
        $form = new InternshipOrganizerPeriodSubscribeGroupForm($period, $this->get_url(array(self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_period_rel_users();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodRelGroupsCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_GROUPS));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodRelGroupsNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_GROUPS));
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
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