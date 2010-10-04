<?php
/**
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/../../calendar/reservations_calendar_week_renderer.class.php';
require_once dirname(__FILE__) . '/../../calendar/reservations_calendar_day_renderer.class.php';
require_once dirname(__FILE__) . '/../../calendar/reservations_calendar_list_renderer.class.php';
require_once dirname(__FILE__) . '/subscription_overview_browser/subscription_overview_browser_table.class.php';
require_once dirname(__FILE__) . '/../../reservations_menu.class.php';
require_once 'Pager/Pager.php';

/**
 * Component to delete an item
 */
class ReservationsManagerOverviewBrowserComponent extends ReservationsManager
{
    const PARAM_CURRENT_ACTION = 'action';
    /**
     * Runs this component and displays its output.
     */

    private $action_bar;

    function run()
    {
        //Header
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Statistics')));

        $this->display_header($trail);

        $current_action = Request :: get(self :: PARAM_CURRENT_ACTION);
        $this->set_parameter(self :: PARAM_CURRENT_ACTION, $current_action);
        $current_action = $current_action ? $current_action : 'day_view';
        
        $this->set_parameter(self :: PARAM_CATEGORY_ID, $this->get_category());

        $this->action_bar = $this->get_action_bar();
        echo $this->action_bar->as_html() . '<br />';

        echo '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        //$actions = array('day_view', 'week_view');
        $actions = array('day_view', 'week_view', 'list_view');
        //$actions = array('week_view', 'list_view');
        foreach ($actions as $action)
        {
            echo '<li><a';
            if ($action == $current_action)
            {
                echo ' class="current"';
            }
            echo ' href="' . $this->get_url(array_merge($this->get_parameters(), array(self :: PARAM_CURRENT_ACTION => $action, 'time' => $this->get_time())), true) . '">' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($action) . 'Title')) . '</a></li>';
        }
        echo '</ul><div class="tabbed-pane-content">';

        $menu = new ReservationsMenu($this->get_category(), '?application=reservations&go=overview&category_id=%s&' . self :: PARAM_CURRENT_ACTION . '=' . $current_action . '&time=' . $this->get_time());
        echo '<div style="float: left; overflow: auto; width: 18%;">' . $menu->render_as_tree() . '</div>';
        
        echo '<div style="float: right; width: 81%;">';
        echo call_user_func(array($this, 'display_' . $current_action));
        echo '</div>';
        
        echo '<div class="clear"></div>';
        echo '</div></div>';

        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageOverview'), Theme :: get_common_image_path() . 'action_statistics.png', $this->get_manage_overview_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (Request :: get(self :: PARAM_CURRENT_ACTION) == 'list_view')
        {
            $action_bar->set_search_url($this->get_url());
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

    function display_week_view()
    {
        $pager_options = array('mode' => 'Sliding', 'perPage' => 5, 'totalItems' => $this->count_selected_overview_items());

        $pager = Pager :: factory($pager_options);
        list($from, $to) = $pager->getOffsetByPageId();

        if ($pager->links)
            echo $pager->links . '<br /><br />';

        // Overview list
        $overview_items = $this->retrieve_overview_items($this->get_condition(), $from - 1, $to);

        if ($overview_items->size() == 0)
            $this->display_message(Translation :: get('NoItemsSelected'));

        while ($overview_item = $overview_items->next_result())
        {
            $item = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $overview_item->get_item_id()))->next_result();
            if (! $item)
                continue;

            echo '<h3>' . $item->get_name() . '</h3>';

            $calendar = new ReservationsCalendarWeekRenderer($this, $this->get_time());
            echo $calendar->render($overview_item->get_item_id());
            echo '<div class="clear">&nbsp;</div><br />';
        }

        // Footer
        echo $pager->links;
    }

    function display_day_view()
    {
        $calendar = new ReservationsCalendarDayRenderer($this, $this->get_time());
        echo $calendar->render($this->retrieve_selected_overview_items());
    }

    function display_list_view()
    {
        $calendar = new ReservationsCalendarListRenderer($this, $this->get_time());
        echo $calendar->render($this->retrieve_selected_overview_items(), $this->action_bar->get_query());
    }
    
    private function count_selected_overview_items()
    {
    	return $this->count_overview_items($this->get_condition());
    }
    
    private function retrieve_selected_overview_items()
    {
    	$overview_items = $this->retrieve_overview_items($this->get_condition());

        if ($overview_items->size() == 0)
            $this->display_message(Translation :: get('NoItemsSelected'));

        $ids = array();

        while ($overview_item = $overview_items->next_result())
        {
            $ids[] = $overview_item->get_item_id();
        }
        
        return $ids;
    }
    
    private function get_condition()
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(OverviewItem :: PROPERTY_USER_ID, $this->get_user_id());
    	$conditions[] = new EqualityCondition(Item :: PROPERTY_CATEGORY, $this->get_category(), Item :: get_table_name());
    	$condition = new AndCondition($conditions);
    	
    	return $condition;
    }
    
    private function get_time()
    {
    	$time = Request :: get('time');
        return (isset($time) ? $time : time());
    }
    
    private function get_category()
    {
    	$category = Request :: get(self :: PARAM_CATEGORY_ID);
    	return (isset($category) ? $category : 0);
    }

}
?>