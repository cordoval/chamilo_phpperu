<?php

require_once dirname(__FILE__) . '/rel_user_browser/rel_user_browser_table.class.php';
require_once dirname(__FILE__) . '/rel_group_browser/rel_group_browser_table.class.php';
require_once dirname(__FILE__) . '/user_browser/user_browser_table.class.php';
require_once Path :: get_application_path().'lib/internship_organizer/publisher/publication_table/publication_table.class.php';


class InternshipOrganizerPeriodManagerViewerComponent extends InternshipOrganizerPeriodManager
{
    const TAB_COORDINATOR = 'cot';
    const TAB_STUDENT = 'stt';
    const TAB_COACH = 'cat';
    const TAB_PUBLICATIONS = 'put';
    
    private $period;
    private $ab;
    private $root_period;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        
        $id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        if ($id)
        {
            $this->period = $this->retrieve_period($id);
            
            $this->root_period = $this->retrieve_periods(new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, 0))->next_result();
            
            $period = $this->period;
            
            if (! $this->get_user()->is_platform_admin())
            {
                Display :: not_allowed();
            }
            
            $trail->add(new Breadcrumb($this->get_browse_periods_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $id)), $period->get_name()));
            $trail->add_help('period general');
            
            $this->display_header($trail);
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html() . '<br />';
            
            echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_period.png);">';
            echo '<div class="title">' . Translation :: get('Details') . '</div>';
            echo '<b>' . Translation :: get('Name') . '</b>: ' . $period->get_name();
            echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $period->get_description();
            echo '<b>' . Translation :: get('Begin') . '</b>: ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatLong'), $period->get_begin());
            echo '<br /><b>' . Translation :: get('End') . '</b>: ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatLong'), $period->get_end());
            
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';
           	echo      $this->get_tabs();     

           	$this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID));
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $condition = new OrCondition($or_conditions);
            
            $periods = InternshipOrganizerDataManager :: get_instance()->retrieve_periods($condition);
            while ($period = $periods->next_result())
            {
                $period_conditions[] = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_ID, $period->get_id());
            }
            
            if (count($period_conditions))
                $conditions[] = new OrCondition($period_conditions);
            else
                $conditions[] = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_ID, 0);
        
        }
        
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    function get_action_bar()
    {
        $period = $this->period;
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period->get_id())));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_period_viewing_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_period_editing_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_period_subscribe_users_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if ($this->period != $this->root_period)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_period_delete_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        //        $condition = new EqualityCondition(InternshipOrganizerPeriodRelLocation :: PROPERTY_PERIOD_ID, $period->get_id());
        //        $locations = $this->retrieve_period_rel_locations($condition);
        //        $visible = ($locations->size() > 0);
        //
        //        if ($visible)
        //        {
        //            $toolbar_data[] = array('href' => $this->get_period_emptying_url($period), 'label' => Translation :: get('Truncate'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
        //            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->get_period_emptying_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        }
        //        else
        //        {
        //            $toolbar_data[] = array('label' => Translation :: get('TruncateNA'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin_na.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
        //            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        }
        

        return $action_bar;
    }

    function get_tabs()
    {
        
    	$renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
    	$tabs = new DynamicTabsRenderer($renderer_name);
             
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        $parameters[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID] = $id;
        
         // Coordinator table tab
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_type_users_condition(InternshipOrganizerUserType :: COORDINATOR));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COORDINATOR, Translation :: get('InternshipOrganizerCoordinator'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Student table tab
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_type_users_condition(InternshipOrganizerUserType :: STUDENT));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_STUDENT, Translation :: get('InternshipOrganizerStudent'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Coach table tab
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_type_users_condition(InternshipOrganizerUserType :: COACH));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COACH, Translation :: get('InternshipOrganizerCoach'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Publication table tab
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publication_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATIONS, Translation :: get('InternshipOrganizerPublications'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    
    }
	
    function get_publication_condition(){
    	
    }
    
    function get_type_users_condition($user_type)
    {
        
        $users = $this->period->get_user_ids($user_type);
        
        if (count($users))
        {
            //        	$conditions = array();
            //        	$conditions[]=new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $user_type);
            //        	$conditions[]=new InCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, $users);
            //        	$type_users_condition = new AndCondition($conditions);
            $type_users_condition = new InCondition(User :: PROPERTY_ID, $users);
        }
        else
        {
            //        	$type_users_condition = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, 0);
            $type_users_condition = new EqualityCondition(User :: PROPERTY_ID, 0);
        }
        
        return $type_users_condition;
    }

}
?>