<?php
require_once dirname(__FILE__) . '/../period_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';
require_once dirname(__FILE__) . '/rel_user_browser/rel_user_browser_table.class.php';

class InternshipOrganizerPeriodManagerBrowserComponent extends InternshipOrganizerPeriodManager
{
    
    const DETAILTAB = 0;
    const GROUPTAB = 1;
    const USERTAB = 2;
    
    private $ab;
    private $period;
    private $root_period;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $trail = new BreadcrumbTrail();
        
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));
        $trail->add_help('period general');
        
        $this->ab = $this->get_action_bar();
        $menu = $this->get_menu_html();
        $output = $this->get_browser_html();
        
        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_browser_html()
    {
        $sub_period_table = new InternshipOrganizerPeriodBrowserTable($this, $this->get_parameters(), $this->get_condition());
        $rel_user_table = new InternshipOrganizerPeriodUserBrowserTable($this, $this->get_parameters(), $this->get_rel_user_condition());
        
        $period = $this->get_period();
        
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        
        $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_period.png);">';
        $html[] = '<div class="title">' . Translation :: get('Details') . '</div>';
        $html[] = '<b>' . Translation :: get('Name') . '</b>: ' . $period->get_name();
        $html[] = '<br /><b>' . Translation :: get('Description') . '</b>: ' . $period->get_description();
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $html[] = '<span>' . Translation :: get('InternshipOrganiserSubPeriods') . '<span>';
        $html[] = $sub_period_table->as_html();
        $html[] = '<br/>';
        $html[] = '<span>' . Translation :: get('InternshipOrganiserRelUsers') . '<span>';
        $html[] = $rel_user_table->as_html();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
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

    function get_condition()
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, $this->get_period()->get_id());
        
        $query = $this->ab->get_query();
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

    function get_rel_user_condition()
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $this->get_period()->get_id());
        
        $query = $this->ab->get_query();
        if (isset($query) && $query != '')
        {
            //            $or_conditions = array();
        //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, '*' . $query . '*');
        //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION, '*' . $query . '*');
        //            $or_condition = new OrCondition($or_conditions);
        //            
        //            $and_conditions = array();
        //            $and_conditions[] = $condition;
        //            $and_conditions[] = $or_condition;
        //            $condition = new AndCondition($and_conditions);
        }
        
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $this->get_period()->get_id())));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerPeriod'), Theme :: get_common_image_path() . 'action_add.png', $this->get_period_create_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewRoot'), Theme :: get_common_image_path() . 'action_home.png', $this->get_browse_periods_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_browse_periods_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ViewPeriod'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_period_viewing_url($this->get_period()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
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