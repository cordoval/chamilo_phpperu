<?php
/**
 * $Id: browser.class.php 240 2009-11-16 14:34:39Z vanpouckesven $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../portfolio_manager_component.class.php';
require_once dirname(__FILE__) . '/user_browser/user_browser_table.class.php';
require_once dirname(__FILE__) . '/../../user_menu.class.php';

/**
 * Portfolio component which allows the user to browse the portfolio application
 * @author Sven Vanpoucke
 */
class PortfolioManagerBrowserComponent extends PortfolioManagerComponent
{
    private $firstletter;
    private $ab;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowsePortfolio')));
        $trail->add_help('portfolio browser');

        $this->ab = $this->get_action_bar();

        $this->display_header($trail);

        $firstletter = Request :: get('firstletter');
        $firstletter = $firstletter ? $firstletter : 'A';
        $this->firstletter = $firstletter;

        $menu = new UserMenu($firstletter);

        echo '<div style="width: 17%; overflow: auto; float: left;">';
        echo $menu->render_as_tree();
        echo '</div>';

        echo '<div style="width: 82%; overflow: auto; float: right;">';
        echo $this->get_table();
        echo '</div>';

        $this->display_footer();
    }

    function get_table()
    {
        $table = new UserBrowserTable($this, array(Application :: PARAM_APPLICATION => 'portfolio', Application :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE), $this->get_condition());
        return $table->as_html();
    }

    function get_condition()
    {
        //search functionality
        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $search_conditions = new OrCondition($or_conditions);
        }

        //place in alfabet-tree
        $firstletter = $this->firstletter;
        $conditions = array();
        if (isset($this->firstletter))
        {
            for($i = 0; $i < 3; $i ++)
            {
                $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, $firstletter . '*');
                if ($firstletter == 'Z')
                    break;
                $firstletter ++;
            }
 
            $condition = new OrCondition($conditions);

            if (count($search_conditions))
                {
                    $condition = new AndCondition($condition, $search_conditions);
                }
        }
        else
        {
            if (count($search_conditions))
            {
                $condition = $search_conditions;
            }
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