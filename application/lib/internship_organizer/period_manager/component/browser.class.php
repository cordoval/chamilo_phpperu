<?php
require_once dirname(__FILE__) . '/../period_manager.class.php';
//require_once dirname(__FILE__) . '/browser/browser_table.class.php';
//require_once dirname(__FILE__) . '/rel_user_browser/rel_user_browser_table.class.php';
//require_once dirname(__FILE__) . '/rel_group_browser/rel_group_browser_table.class.php';
//require_once dirname(__FILE__) . '/rel_category_browser/rel_category_browser_table.class.php';


class InternshipOrganizerPeriodManagerBrowserComponent extends InternshipOrganizerPeriodManager
{
    
    const TAB_DETAILS = 'dett';
    const TAB_SUBPERIODS = 'subt';
    const TAB_GROUPS = 'grot';
    const TAB_USERS = 'uset';
    const TAB_CATEGORIES = 'catt';
    
    private $action_bar;
    private $period;
    private $root_period;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
             
    	if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $period_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        $period = $this->retrieve_period($period_id);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('period general');
        
        $this->action_bar = $this->get_action_bar();
        $menu = $this->get_menu_html();
        $output = $this->get_browser_html();
        
        $this->display_header($trail);
        echo $this->action_bar->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_browser_html()
    {
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $parameters[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID] = $id;
        
        // Users table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_USERS;
        $table = new InternshipOrganizerPeriodRelUserBrowserTable($this, $parameters, $this->get_rel_users_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_USERS, Translation :: get('Users'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Group table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_GROUPS;
        $table = new InternshipOrganizerPeriodRelGroupBrowserTable($this, $parameters, $this->get_rel_groups_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_GROUPS, Translation :: get('Groups'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Category table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_CATEGORIES;
        $table = new InternshipOrganizerCategoryRelPeriodBrowserTable($this, $parameters, $this->get_rel_category_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_CATEGORIES, Translation :: get('Categories'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Period info tab
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAILS, Translation :: get('Details'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $this->get_period_info()));
        
        // Sub periods
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_SUBPERIODS;
        $table = new InternshipOrganizerPeriodBrowserTable($this, $parameters, $this->get_sub_periods_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_SUBPERIODS, Translation :: get('InternshipOrganizerSubPeriods'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_period_info()
    {
        $period = $this->get_period();
        
        $html = array();
        
        $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_period.png);">';
        $html[] = '<div class="title">' . Translation :: get('Details') . '</div>';
        $html[] = '<b>' . Translation :: get('Name') . '</b>: ' . $period->get_name();
        $html[] = '<br /><b>' . Translation :: get('Description') . '</b>: ' . $period->get_description();
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    function get_menu_html()
    {
        $period_menu = new InternshipOrganizerPeriodMenu($this->get_period()->get_id());
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $period_menu->render_as_tree();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    function get_period()
    {
        if (! $this->period)
        {
            $period_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
            
            if (! $period_id)
            {
                $this->period = $this->get_root_period();
            }
            else
            {
                $this->period = $this->retrieve_period($period_id);
            }
        
        }
        
        return $this->period;
    }

    function get_root_period()
    {
        if (! $this->root_period)
        {
            $this->root_period = $this->retrieve_root_period();
        }
        
        return $this->root_period;
    }

    function get_sub_periods_condition()
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, $this->get_period()->get_id());
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);
            
            $and_conditions = array();
            $and_conditions[] = $condition;
            $and_conditions[] = $or_condition;
            $condition = new AndCondition($and_conditions);
        }
        
        return $condition;
    }

    function get_rel_users_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $this->get_period()->get_id());
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function get_rel_groups_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelGroup :: PROPERTY_PERIOD_ID, $this->get_period()->get_id());
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            
            $group_alias = GroupDataManager :: get_instance()->get_alias(Group :: get_table_name());
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(Group :: PROPERTY_NAME, '*' . $query . '*', $group_alias, true);
            $search_conditions[] = new PatternMatchCondition(Group :: PROPERTY_DESCRIPTION, '*' . $query . '*', $group_alias, true);
            $conditions = new OrCondition($search_conditions);
        
        }
        
        return new AndCondition($conditions);
    }

    function get_rel_category_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelPeriod :: PROPERTY_PERIOD_ID, $this->get_period()->get_id());
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerCategory :: PROPERTY_NAME, '*' . $query . '*', InternshipOrganizerCategory :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerCategory :: PROPERTY_DESCRIPTION, '*' . $query . '*', InternshipOrganizerCategory :: get_table_name());
            $conditions[] = new OrCondition($search_conditions);
        
        }
        
        return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $this->get_period()->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => Request :: get(DynamicTabsRenderer :: PARAM_SELECTED_TAB))));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewRoot'), Theme :: get_common_image_path() . 'action_home.png', $this->get_browse_periods_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_browse_periods_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_period_publish_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerPeriod'), Theme :: get_common_image_path() . 'action_create.png', $this->get_period_create_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_period_editing_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ViewPeriod'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_period_viewing_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_USER_RIGHT, $this->period->get_id(), InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddInternshipOrganizerUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_period_subscribe_user_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_GROUP_RIGHT, $this->period->get_id(), InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddInternshipOrganizerGroups'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_period_subscribe_group_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_CATEGORY_RIGHT, $this->period->get_id(), InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddCategories'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_period_subscribe_category_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_REPORTING, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
                {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Reporting'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_period_reporting_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        return $action_bar;
    }

    function get_browse_period_tabs($links)
    {
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $browse_period_tabs = new DynamicTabsRenderer($renderer_name);
        
        $index = 0;
        foreach ($links as $sub_manager_links)
        {
            if (count($sub_manager_links['links']))
            {
                $index ++;
                $html = array();
                
                if (isset($sub_manager_links['search']))
                {
                    $search_form = new AdminSearchForm($this, $sub_manager_links['search'], $index);
                    
                    $html[] = '<div class="vertical_action" style="border-top: none;">';
                    $html[] = '<div class="icon">';
                    $html[] = '<img src="' . Theme :: get_image_path('internship_organizer') . 'browse_search.png" alt="' . Translation :: get('Search') . '" title="' . Translation :: get('Search') . '"/>';
                    $html[] = '</div>';
                    $html[] = $search_form->display();
                    $html[] = '</div>';
                }
                
                $count = 1;
                
                foreach ($sub_manager_links['links'] as $link)
                {
                    $count ++;
                    
                    if ($link['confirm'])
                    {
                        $onclick = 'onclick = "return confirm(\'' . $link['confirm'] . '\')"';
                    }
                    
                    if (! isset($sub_manager_links['search']) && $application_settings_count == 0 && $count == 2)
                    {
                        $html[] = '<div class="vertical_action" style="border-top: none;">';
                    }
                    else
                    {
                        $html[] = '<div class="vertical_action">';
                    }
                    
                    $html[] = '<div class="icon">';
                    $html[] = '<a href="' . $link['url'] . '" ' . $onclick . '><img src="' . Theme :: get_image_path('internship_organizer') . 'browse_' . $link['action'] . '.png" alt="' . $link['name'] . '" title="' . $link['name'] . '"/></a>';
                    $html[] = '</div>';
                    $html[] = '<div class="description">';
                    $html[] = '<h4><a href="' . $link['url'] . '" ' . $onclick . '>' . $link['name'] . '</a></h4>';
                    $html[] = $link['description'];
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
                
                $browse_period_tabs->add_tab(new DynamicActionsTab($sub_manager_links['application']['class'], Translation :: get($sub_manager_links['application']['name']), Theme :: get_image_path() . 'place_mini_' . $sub_manager_links['application']['class'] . '.png', implode("\n", $html), Tab :: TYPE_ACTIONS));
            }
        }
        
        return $browse_period_tabs->render();
    }

}
?>