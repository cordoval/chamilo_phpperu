<?php


namespace application\portfolio;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Request;
use user\UserMenu;
use common\libraries\ActionBarSearchForm;
use common\libraries\Application;
use user\User;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\SubselectCondition;
use common\libraries\AndCondition;
use common\libraries\ActionBarRenderer;

require_once dirname(__FILE__) . '/portfolio_browser/portfolio_browser_table.class.php';

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
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseObject',  array('OBJECT' => Translation::get('Portfolio')), Utilities::COMMON_LIBRARIES)));
        $trail->add_help('portfolio browser');

        $html = array();

        $this->ab = $this->get_action_bar();

        $this->display_header($trail);

        $firstletter = Request :: get('firstletter');
        $firstletter = $firstletter ? $firstletter : '-';
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
         $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        $parameters[Application :: PARAM_APPLICATION] = 'portfolio';
        $parameters[Application :: PARAM_ACTION] =  PortfolioManager :: ACTION_BROWSE;


        $table = new PortfolioBrowserTable($this, array(Application :: PARAM_APPLICATION => 'portfolio', Application :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE), $this->get_condition());
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
                if($this->firstletter == '-')
                {
                    //just show the first results

                }
                else
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
        }

        $conditions[] = new SubselectCondition(User::PROPERTY_ID, PortfolioInformation::PROPERTY_USER_ID, PortfolioInformation::get_table_name(), null, null, PortfolioDataManager::get_instance());

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