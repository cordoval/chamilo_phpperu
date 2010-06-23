<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';

class InternshipOrganizerAgreementManagerBrowserComponent extends InternshipOrganizerAgreementManager
{
      
	const TAB_ADD_LOCATION = 1;
    const TAB_TO_APPROVE = 2;
    const TAB_APPROVED = 3;
    
    private $action_bar;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
              
        $table = $this->get_table(InternshipOrganizerAgreement::STATUS_ADD_LOCATION);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_ADD_LOCATION, InternshipOrganizerAgreement :: get_status_name(InternshipOrganizerAgreement::STATUS_ADD_LOCATION), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $table = $this->get_table(InternshipOrganizerAgreement::STATUS_TO_APPROVE);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_TO_APPROVE, InternshipOrganizerAgreement :: get_status_name(InternshipOrganizerAgreement::STATUS_TO_APPROVE), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $table = $this->get_table(InternshipOrganizerAgreement::STATUS_APPROVED);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_APPROVED, InternshipOrganizerAgreement :: get_status_name(InternshipOrganizerAgreement::STATUS_APPROVED), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        
        echo $tabs->render();
//        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table($agreement_type)
    {
        $parameters = $this->get_parameters();
        $table = new InternshipOrganizerAgreementBrowserTable($this, $parameters, $this->get_condition($agreement_type));
        return $table;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerAgreement'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_agreement_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $action_bar->set_search_url($this->get_url());
        
        return $action_bar;
    }

    function get_condition($agreement_type)
    {
        
        $query = $this->action_bar->get_query();
        $condition = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_STATUS, $agreement_type);
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_NAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            
            $user_conditions = array();
            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $user_condition = new OrCondition($user_conditions);
            
            $udm = UserDataManager :: get_instance();
            $users = $udm->retrieve_users($user_condition);
            
            $user_ids = array();
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }
            
            if (count($user_ids))
            {
                
                $search_conditions[] = new InCondition(InternshipOrganizerAgreement :: PROPERTY_STUDENT_ID, $user_ids);
            
            }
            
            $dm = InternshipOrganizerDataManager :: get_instance();
            $period_condition = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, '*' . $query . '*');
            $periods = $dm->retrieve_periods($period_condition);
            
            $period_ids = array();
            while ($period = $periods->next_result())
            {
                $period_ids[] = $period->get_id();
            }
            
            if (count($period_ids))
            {
                
                $search_conditions[] = new InCondition(InternshipOrganizerAgreement :: PROPERTY_PERIOD_ID, $period_ids);
            
            }
            
            $or_condition = new OrCondition($search_conditions);
        	return new AndCondition(array($condition, $or_condition));
        }
        
        return $condition;
    }
}
?>