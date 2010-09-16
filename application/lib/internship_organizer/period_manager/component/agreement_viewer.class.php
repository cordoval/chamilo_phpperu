<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/viewer.class.php';

require_once dirname(__FILE__) . '/../period_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/agreement_user/agreement_user_table.class.php';

class InternshipOrganizerPeriodManagerAgreementViewerComponent extends InternshipOrganizerPeriodManager
{
    
    const TAB_COORDINATOR = 'cort';
    const TAB_COACH = 'cact';
    const TAB_STUDENT = 'stut';
    const TAB_DETAIL = 'dett';
    
    private $action_bar;
    private $agreement;
    private $period;
    private $student;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement_id = $_GET[InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID];
        $this->agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($agreement_id);
        $period_id = $this->agreement->get_period_id();
       
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->period = InternshipOrganizerDataManager :: get_instance()->retrieve_period($period_id);
        
        $trail = BreadcrumbTrail :: get_instance();
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div>';
        echo $this->get_tabs();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_tabs()
    {
        
        $html = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
        
        // Coordinators
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_COORDINATOR;
        $table = new InternshipOrganizerPeriodAgreementUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: COORDINATOR), InternshipOrganizerUserType :: COORDINATOR);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COORDINATOR, Translation :: get('InternshipOrganizerCoordinators'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Coaches
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_COACH;
        $table = new InternshipOrganizerPeriodAgreementUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: COACH), InternshipOrganizerUserType :: COACH);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COACH, Translation :: get('InternshipOrganizerCoaches'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Student
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_STUDENT;
        $table = new InternshipOrganizerPeriodAgreementUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: STUDENT), InternshipOrganizerUserType :: STUDENT);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_STUDENT, Translation :: get('InternshipOrganizerStudent'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Detail tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_DETAIL;
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAIL, Translation :: get('InternshipOrganizerDetail'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $this->get_detail()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id())));
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_AGREEMENT_USER_RIGHT, $this->period->get_id(), InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddCoördinaters'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_subscribe_agreement_rel_user_url($this->agreement, InternshipOrganizerUserType :: COORDINATOR), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddCoaches'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_subscribe_agreement_rel_user_url($this->agreement, InternshipOrganizerUserType :: COACH), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        }
        
        return $action_bar;
    }

    function get_users_condition($user_type)
    {
        $query = $this->action_bar->get_query();
        $conditions = array();
        
        $user_ids = $this->agreement->get_user_ids($user_type);
        
        if (count($user_ids))
        {
            $conditions[] = new InCondition(User :: PROPERTY_ID, $user_ids);
        }
        else
        {
            $conditions[] = new EqualityCondition(User :: PROPERTY_ID, 0);
        }
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            //            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

    function get_detail()
    {
        $html = array();
        $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_location.png);">';
        $html[] = '<div class="title">' . Translation :: get('Details') . '</div>';
        $html[] = '<b>' . Translation :: get('Name') . '</b>: ' . $this->agreement->get_name() . '<br /> ';
        $html[] = '<b>' . Translation :: get('Description') . '</b>: ' . $this->agreement->get_description() . '<br /> ';
        
        $student = $this->get_student();
        
        $html[] = '<div class="title">' . Translation :: get('Student') . '</div>';
        $html[] = '<b>' . Translation :: get('Firstname') . '</b>: ' . $student->get_firstname();
        $html[] = '<br /><b>' . Translation :: get('Lastname') . '</b>: ' . $student->get_lastname();
        $html[] = '<br /><b>' . Translation :: get('InternshipOrganizerEmail') . '</b>: ' . $student->get_email();
        
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        return implode($html, "\n");
    }

    function get_student()
    {
        $student_id = $this->agreement->get_user_ids(InternshipOrganizerUserType :: STUDENT);
        $dm = UserDataManager :: get_instance();
        $student = $dm->retrieve_user($student_id[0]);
        return $student;
    
    }

    function get_agreement()
    {
        return $this->agreement;
    }

    function get_period()
    {
        return $this->period;
    }

}
?>