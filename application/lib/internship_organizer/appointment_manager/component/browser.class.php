<?php
require_once dirname(__FILE__) . '/moment_browser/table.class.php';

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
        $table = new InternshipOrganizerMomentRelUserBrowserTable($this, $parameters, $this->get_moment_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_MOMENTS, Translation :: get('Moments'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
       
       
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }
   
    function get_moment_condition()
    {
        
    	$moment_alias = InternshipOrganizerDataManager::get_instance()->get_alias(InternshipOrganizerMoment :: get_table_name());
        $agreement_rel_user_alias = InternshipOrganizerDataManager::get_instance()->get_alias(InternshipOrganizerAgreementRelUser :: get_table_name());
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
    	
        
        
    	$condition = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $this->get_user_id(), $agreement_rel_user_alias, true);
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);
            
            $and_conditions = array();
            $and_conditions[] = $condition;
            $and_conditions[] = $or_condition;
            $condition = new AndCondition($and_conditions);
        }
        
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
               
        return $action_bar;
    }
}
?>