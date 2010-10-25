<?php
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'appointment_manager/component/moment_rel_location/table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'appointment_manager/component/appointment/table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_rel_user.class.php';

class InternshipOrganizerAppointmentManagerBrowserComponent extends InternshipOrganizerAppointmentManager
{
    
    const TAB_MOMENTS = 1;
    const TAB_APPOINTMENTS = 2;
    
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_APPOINTMENT, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->action_bar = $this->get_action_bar();
        
        $output = $this->get_browser_html();
        
        $this->display_header();
        echo $this->action_bar->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_browser_html()
    {
        
        $html = array();
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        // Moment table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_MOMENTS;
        $table = new InternshipOrganizerMomentRelLocationBrowserTable($this, $parameters, $this->get_moment_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_MOMENTS, Translation :: get('Moments'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', '<div style="overflow: auto;">' . $table->as_html() . '<div class="clear"></div></div>'));
        
        // Appointment table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_APPOINTMENTS;
        $table = new InternshipOrganizerAppointmentBrowserTable($this, $parameters, $this->get_appointment_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_APPOINTMENTS, Translation :: get('Appointments'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_moment_condition()
    {
        
        $condition = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $this->get_user_id());
        $agreement_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_users($condition);
        $agreement_ids = array();
        while ($agreement_rel_user = $agreement_rel_users->next_result())
        {
            $agreement_ids[] = $agreement_rel_user->get_agreement_id();
        }
        $agreement_ids = array_unique($agreement_ids);
        
        $moment_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerMoment :: get_table_name());
        $agreement_rel_user_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerAgreementRelUser :: get_table_name());
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $location_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerLocation :: get_table_name());
        $region_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerRegion :: get_table_name());
        $agreement_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerAgreement :: get_table_name());
        $period_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerPeriod :: get_table_name());
        
        
        $conditions = array();
        if (count($agreement_ids))
        {
            $conditions[] = new InCondition(InternshipOrganizerMoment :: PROPERTY_AGREEMENT_ID, $agreement_ids, $moment_alias, true);
        }
        else
        {
            $conditions[] = new EqualityCondition(InternshipOrganizerMoment :: PROPERTY_AGREEMENT_ID, 0, $moment_alias, true);
        }
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_NAME, '*' . $query . '*', $moment_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_DESCRIPTION, '*' . $query . '*', $moment_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_ADDRESS, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_TELEPHONE, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*', $region_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*', $region_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_NAME, '*' . $query . '*', $agreement_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION, '*' . $query . '*', $agreement_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, '*' . $query . '*', $period_alias, true);
            
            
            $conditions[] = new OrCondition($search_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function get_appointment_condition()
    {
        
        $moment_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerMoment :: get_table_name());
        $agreement_rel_user_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerAgreementRelUser :: get_table_name());
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $appointment_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerAppointment :: get_table_name());
        
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAppointment :: PROPERTY_OWNER_ID, $this->get_user_id(), $appointment_alias, true);
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAppointment :: PROPERTY_TITLE, '*' . $query . '*', $appointment_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAppointment :: PROPERTY_STATUS, '*' . $query . '*', $appointment_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION, '*' . $query . '*', $appointment_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        return $action_bar;
    }
}
?>