<?php
/**
 * $Id: item_browser.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/item_browser/item_browser_table.class.php';
require_once dirname(__FILE__) . '/../../reservations_menu.class.php';
require_once dirname(__FILE__) . '/../../forms/pool_form.class.php';
require_once dirname(__FILE__) . '/subscription_browser/user_quota_cellrenderer.class.php';

class ReservationsManagerItemBrowserComponent extends ReservationsManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewItems')));

        $this->ab = $this->get_action_bar();
        $menu = new ReservationsMenu($_GET[ReservationsManager :: PARAM_CATEGORY_ID], '?application=reservations&go=browse_items&category_id=%s');
        $poolform = $this->get_poolform();

        $this->display_header($trail);
        
        echo $this->ab->as_html() . '<br />';
        echo '<div style="float: left; overflow: auto; width: 18%;">' . $menu->render_as_tree() . '</div>';
        echo '<div style="float: right; width: 80%;">';
        
        if ($poolform && $this->has_right(ReservationsRights :: TYPE_CATEGORY, $this->get_category(), ReservationsRights :: VIEW_RIGHT))
        {
            echo $poolform->display();
        }
            
        echo '<br />';
        echo $this->get_user_html();
        echo '</div><div class="clear">&nbsp;</div>';

        $used_quota = ReservationsDataManager :: calculate_used_quota('1', $this->get_category(), $this->get_user_id());

        $table = new SimpleTable($used_quota, new UserQuotaCellRenderer(), null, 'user_quota');
        echo '<br /><br /><h3>' . Translation :: get('UsedCredits') . '</h3>' . $table->toHTML();

        $this->display_footer();
    }

    function get_user_html()
    {
        $table = new ItemBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category()), $this->get_condition());

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

    function get_poolform()
    {
        $category = $this->retrieve_categories(new EqualityCondition(Category :: PROPERTY_ID, $this->get_category()))->next_result();
        if ($category && $category->use_as_pool() && $this->has_right(ReservationsRights :: TYPE_CATEGORY, $category->get_id(), ReservationsRights :: MAKE_RESERVATION_RIGHT))
        {
            $form = new PoolForm($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_SEARCH_POOL, ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category())), $this->get_user());
            return $form;
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>