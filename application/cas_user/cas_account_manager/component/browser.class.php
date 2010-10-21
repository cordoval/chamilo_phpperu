<?php
require_once dirname(__FILE__) . '/cas_account_browser/cas_account_browser_table.class.php';

class CasAccountManagerBrowserComponent extends CasAccountManager
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('cas_user general');

        $this->action_bar = $this->get_action_bar();

        $this->display_header($trail);
        echo '<a name="top"></a>';
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        $table = new CasAccountBrowserTable($this, $this->get_parameters(), $this->get_condition());
        echo $table->as_html();
        echo '</div>';
        $this->display_footer();
    }

    function get_condition()
    {
        $user = $this->get_user();
        $query = $this->action_bar->get_query();
        $conditions = array();

        if (isset($query) && $query != '')
        {
            $query_conditions = array();
            $query_conditions[] = new PatternMatchCondition(CasAccount :: PROPERTY_FIRST_NAME, '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(CasAccount :: PROPERTY_LAST_NAME, '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(CasAccount :: PROPERTY_EMAIL, '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(CasAccount :: PROPERTY_GROUP, '*' . $query . '*');
            $conditions[] = new OrCondition($query_conditions);
        }

        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            $this->action_bar->set_search_url($this->get_url());

            if ($this->get_user()->is_platform_admin())
            {
                $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateAccount'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(
                        CasAccountManager :: PARAM_CAS_ACCOUNT_ACTION => CasAccountManager :: ACTION_CREATE))));
            }
        }
        return $this->action_bar;
    }
}
?>