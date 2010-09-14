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
        
        $period_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        $location_id = InternshipOrganizerRights :: get_location_id_by_identifier_from_internship_organizers_subtree($period_id, InternshipOrganizerRights :: TYPE_PERIOD);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_USER_RIGHT, $location_id, InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('period subscribe users');
        
        $this->period = $this->retrieve_period($period_id);
        
        $form = new InternshipOrganizerPeriodSubscribeUserForm($this->period, $this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_period_rel_users();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodRelUsersCreated'), (false), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $this->period->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_USERS));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodRelUsersNotCreated'), (true), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $this->period->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_USERS));
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