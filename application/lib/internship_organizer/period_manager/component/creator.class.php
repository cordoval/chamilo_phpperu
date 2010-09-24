<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/period_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/browser.class.php';

class InternshipOrganizerPeriodManagerCreatorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $period = new InternshipOrganizerPeriod();
        $parent_id = Request :: get(self :: PARAM_PERIOD_ID);
        
        $period->set_parent_id($parent_id);
        $period->set_owner($this->get_user_id());
        $form = new InternshipOrganizerPeriodForm(InternshipOrganizerPeriodForm :: TYPE_CREATE, $period, $this->get_url(array(self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_period();
            if ($success)
            {
                $period = $form->get_period();

                $parameters = array();
                $parameters[InternshipOrganizerChangesTracker :: PROPERTY_OBJECT_ID] = $period->get_id();
                $parameters[InternshipOrganizerChangesTracker :: PROPERTY_OBJECT_TYPE] = InternshipOrganizerChangesTracker :: TYPE_PERIOD;
                $parameters[InternshipOrganizerChangesTracker :: PROPERTY_EVENT_TYPE] = InternshipOrganizerChangesTracker :: CREATE_EVENT;
                $parameters[InternshipOrganizerChangesTracker :: PROPERTY_USER_ID] = $this->get_user_id();
                Event :: trigger(InternshipOrganizerChangesTracker :: CREATE_EVENT, InternshipOrganizerManager :: APPLICATION_NAME, $parameters);
                
                $this->redirect(Translation :: get('InternshipOrganizerPeriodCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $period->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_SUBPERIODS));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $parent_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_SUBPERIODS));
            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS)), Translation :: get('BrowseInternshipOrganizerPeriods')));
    }

}
?>