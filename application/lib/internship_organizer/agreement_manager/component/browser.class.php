<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/user_type.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_rel_user.class.php';

class InternshipOrganizerAgreementManagerBrowserComponent extends InternshipOrganizerAgreementManager
{
    
    const TAB_ADD_LOCATION = 'addl';
    const TAB_TO_APPROVE = 'toap';
    const TAB_APPROVED = 'appr';
    const TAB_COACH = 'coat';
    const TAB_COORDINATOR = 'coot';
    const TAB_STUDENT = 'stut';
    const TAB_MENTOR = 'ment';
    
    private $action_bar;
    private $user_id;

    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_AGREEMENT, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $this->user_id = $this->get_user_id();
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $dm = InternshipOrganizerDataManager :: get_instance();
        
        
        $coordinator = InternshipOrganizerUserType :: COORDINATOR;
        $coordinator_count = $dm->count_agreements($this->get_condition(InternshipOrganizerAgreement :: STATUS_APPROVED, $coordinator));
        if ($coordinator_count > 0)
        {
            $table = $this->get_table(InternshipOrganizerAgreement :: STATUS_APPROVED,$coordinator);
            $tabs->add_tab(new DynamicContentTab(self :: TAB_COORDINATOR, Translation :: get('InternshipOrganizerCoordinator'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        }
        
        $coach = InternshipOrganizerUserType :: COACH;
        $coach_count = $dm->count_agreements($this->get_condition(InternshipOrganizerAgreement :: STATUS_APPROVED, $coach));
        if ($coach_count > 0)
        {
            $table = $this->get_table(InternshipOrganizerAgreement :: STATUS_APPROVED, $coach);
            $tabs->add_tab(new DynamicContentTab(self :: TAB_COACH, Translation :: get('InternshipOrganizerCoach'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        }
        
        $student = InternshipOrganizerUserType :: STUDENT;
        $student_count = $dm->count_agreements($this->get_condition(InternshipOrganizerAgreement :: STATUS_APPROVED, $student));
        if ($student_count > 0)
        {
            $table = $this->get_table(InternshipOrganizerAgreement :: STATUS_APPROVED, $student);
            $tabs->add_tab(new DynamicContentTab(self :: TAB_STUDENT, Translation :: get('InternshipOrganizerStudent'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        }
        
        $mentor = InternshipOrganizerUserType :: MENTOR;
        $mentor_count = $dm->count_agreements($this->get_condition(InternshipOrganizerAgreement :: STATUS_APPROVED, $mentor));
        if ($mentor_count > 0)
        {
            $table = $this->get_table(InternshipOrganizerAgreement :: STATUS_APPROVED, $mentor);
            $tabs->add_tab(new DynamicContentTab(self :: TAB_MENTOR, Translation :: get('InternshipOrganizerMentor'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        }
        
        $coordinator_coach_student = array(InternshipOrganizerUserType :: COACH, InternshipOrganizerUserType :: COORDINATOR, InternshipOrganizerUserType :: STUDENT);
        
        $coordinator_coach_count = $dm->count_agreements($this->get_condition(InternshipOrganizerAgreement :: STATUS_ADD_LOCATION, $coordinator_coach_student));
        if ($coordinator_coach_count > 0)
        {
            $table = $this->get_table(InternshipOrganizerAgreement :: STATUS_ADD_LOCATION, $coordinator_coach_student);
            $tabs->add_tab(new DynamicContentTab(self :: TAB_ADD_LOCATION, InternshipOrganizerAgreement :: get_status_name(InternshipOrganizerAgreement :: STATUS_ADD_LOCATION), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        }
        
        $coordinator_coach_count = $dm->count_agreements($this->get_condition(InternshipOrganizerAgreement :: STATUS_TO_APPROVE, $coordinator_coach_student));
        if ($coordinator_coach_count > 0)
        {
            $table = $this->get_table(InternshipOrganizerAgreement :: STATUS_TO_APPROVE, $coordinator_coach_student);
            $tabs->add_tab(new DynamicContentTab(self :: TAB_TO_APPROVE, InternshipOrganizerAgreement :: get_status_name(InternshipOrganizerAgreement :: STATUS_TO_APPROVE), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        }
        
        echo $tabs->render();
        //        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table($agreement_type, $user_types)
    {
        $parameters = $this->get_parameters();
        $table = new InternshipOrganizerAgreementBrowserTable($this, $parameters, $this->get_condition($agreement_type, $user_types));
        return $table;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, InternshipOrganizerRights :: LOCATION_AGREEMENT, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishInAgreements'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_agreements_publish_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_AGREEMENT, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerAgreement'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_agreement_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_REPORTING, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Reporting'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_agreement_reporting_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        $action_bar->set_search_url($this->get_url());
        
        return $action_bar;
    }

    function get_condition($agreement_type, $user_types)
    {
        
        $conditions = array();
        $agreement_ids = $this->get_agreement_ids($user_types);
        if (count($agreement_ids) > 0)
        {
            $conditions[] = new InCondition(InternshipOrganizerAgreement :: PROPERTY_ID, $agreement_ids);
            $conditions[] = new InCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, InternshipOrganizerUserType :: STUDENT, InternshipOrganizerAgreementRelUser :: get_table_name());
            $conditions[] = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_STATUS, $agreement_type);
            
            $query = $this->action_bar->get_query();
            if (isset($query) && $query != '')
            {
                $search_conditions = array();
                $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_NAME, '*' . $query . '*');
                $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION, '*' . $query . '*');
                $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
                $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
                $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
                $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*', $user_alias, true);
                $search_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, '*' . $query . '*', InternshipOrganizerPeriod :: get_table_name());
                $conditions[] = new OrCondition($search_conditions);
            
            }
            
            return new AndCondition($conditions);
        }
        else
        {
            return new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_ID, 0);
        }
    
    }

    function get_agreement_ids($user_types)
    {
        
        if (! is_array($user_types))
        {
            $user_types = array($user_types);
        }
        
        $conditions = array();
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $conditions[] = new EqualityCondition(User :: PROPERTY_ID, $this->user_id, $user_alias, true);
        $conditions[] = new InCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, $user_types, InternshipOrganizerAgreementRelUser :: get_table_name());
        $condition = new AndCondition($conditions);
        $agreements = InternshipOrganizerDataManager :: get_instance()->retrieve_agreements($condition);
        $agreement_ids = array();
        while ($agreement = $agreements->next_result())
        {
            $agreement_ids[] = $agreement->get_id();
        }
        
        if (in_array(InternshipOrganizerUserType :: MENTOR, $user_types))
        {
            
            $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
            $condition = new EqualityCondition(User :: PROPERTY_ID, $this->user_id, $user_alias, true);
            $agreements = InternshipOrganizerDataManager :: get_instance()->retrieve_mentor_agreements($condition);
            while ($agreement = $agreements->next_result())
            {
                $agreement_ids[] = $agreement->get_id();
            }
        }
        
        return array_unique($agreement_ids);
    }

}
?>