<?php
/**
 * $Id: admin_item_browser.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/item_browser/item_browser_table.class.php';
require_once dirname(__FILE__) . '/../../reservations_menu.class.php';
require_once dirname(__FILE__) . '/../../forms/pool_form.class.php';

class ReservationsManagerAdminItemBrowserComponent extends ReservationsManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageItems')));

        $this->ab = $this->get_action_bar();
        $menu = new ReservationsMenu($_GET[ReservationsManager :: PARAM_CATEGORY_ID], '?application=reservations&go=admin_browse_items&category_id=%s');

        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo '<div style="float: left; width: 18%; overflow: auto;">' . $menu->render_as_tree() . '</div>';
        echo '<div style="float: right; width: 80%;">';
        echo $this->get_user_html();
        echo '</div>';
        $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = array_merge($this->get_parameters(), array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category()));
        $table = new ItemBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {
        $cat_id = $this->get_category();
        $conditions[] = new EqualityCondition(Item :: PROPERTY_CATEGORY, $cat_id);
        $conditions[] = new EqualityCondition(Item :: PROPERTY_STATUS, Item :: STATUS_NORMAL);
        $condition = new AndCondition($conditions);

        $search = $this->ab->get_query();
        if (isset($search) && ($search != ''))
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(Item :: PROPERTY_NAME, '*' . $search . '*');
            $conditions[] = new PatternMatchCondition(Item :: PROPERTY_DESCRIPTION, '*' . $search . '*');
            $orcondition = new OrCondition($conditions);

            $conditions = array();
            $conditions[] = $orcondition;
            $conditions[] = $condition;
            $condition = new AndCondition($conditions);
        }
        return $condition;
    }

    function get_category()
    {
        return (isset($_GET[ReservationsManager :: PARAM_CATEGORY_ID]) ? $_GET[ReservationsManager :: PARAM_CATEGORY_ID] : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category())));

        if ($this->get_category() == 0)
        {
            $bool = $this->has_right('root', 0, ReservationsRights :: ADD_RIGHT);
        }
        else
        {
            $bool = $this->has_right('category', $this->get_category(), ReservationsRights :: ADD_RIGHT);
        }

        if ($bool)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_item_url($this->get_category()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->get_category() == 0)
        {
            $bool = $this->has_right('root', 0, ReservationsRights :: EDIT_RIGHT);
        }
        else
        {
            $bool = $this->has_right('category', $this->get_category(), ReservationsRights :: EDIT_RIGHT);
        }

        if ($bool)
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Blackout'), Theme :: get_common_image_path() . 'action_lock.png', $this->get_blackout_category_url($this->get_category(), 1), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('UnBlackout'), Theme :: get_common_image_path() . 'action_unlock.png', $this->get_blackout_category_url($this->get_category(), 0), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('SetCredits'), Theme :: get_common_image_path() . 'action_statistics.png', $this->get_credit_category_url($this->get_category()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }
}
?>