<?php
/**
 * $Id: browser.class.php 240 2009-11-16 14:34:39Z vanpouckesven $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/user_browser/user_browser_table.class.php';
require_once dirname(__FILE__) . '/../../user_menu.class.php';
require_once dirname(__FILE__) . '/../../portfolio_publication.class.php';

/**
 * Portfolio component which allows the user to browse the portfolio application
 * @author Sven Vanpoucke
 */
class PortfolioManagerBrowserComponent extends PortfolioManager
{
    private $firstletter;
    private $ab;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowsePortfolio')));
        $trail->add_help('portfolio browser');

        $html = array();

        $this->ab = $this->get_action_bar();

        $this->display_header($trail);

        $firstletter = Request :: get('firstletter');
        $firstletter = $firstletter ? $firstletter : 'A';
        $this->firstletter = $firstletter;

        $menu = new UserMenu($firstletter);
        $html[] = $this->ab->as_html() . '<br />';
        $html[] =  '<div style="width: 17%; overflow: auto; float: left;">';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div style="width: 82%; overflow: auto; float: right;">';
        $html[] = $this->get_table();
        $html[] = '</div>';
        echo implode("\n", $html);
        $this->display_footer();
    }

    function get_table()
    {
        $table = new UserBrowserTable($this, array(Application :: PARAM_APPLICATION => 'portfolio', Application :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE), $this->get_condition());
        return $table->as_html();
    }

    function get_condition()
    {
        
        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $conditions[] = new OrCondition($search_conditions);
        }
        else
        {
            //place in alfabet-tree
            $firstletter = $this->firstletter;
            $conditions = array();
            if (isset($this->firstletter))
            {
                for($i = 0; $i < 3; $i ++)
                {
                    $tree_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, $firstletter . '*');
                    if ($firstletter == 'Z')
                        break;
                    $firstletter ++;
                }
                $conditions[] = new OrCondition($tree_conditions);
            }
        }

        // TODO: find the correct way to add the DISTINCT
        $conditions[] = new SubselectCondition(User::PROPERTY_ID, PortfolioPublication::PROPERTY_PUBLISHER, PortfolioPublication::get_table_name());

        $condition = new AndCondition($conditions);

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