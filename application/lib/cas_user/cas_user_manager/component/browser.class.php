<?php
require_once dirname(__FILE__) . '/cas_user_request_browser/cas_user_request_browser_table.class.php';

class CasUserManagerBrowserComponent extends CasUserManager
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
        $table = new CasUserRequestBrowserTable($this, array(Application :: PARAM_APPLICATION => CasUserManager :: APPLICATION_NAME, Application :: PARAM_ACTION => CasUserManager :: ACTION_BROWSE), $this->get_condition());
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
            $query_conditions[] = new PatternMatchCondition(CasUserRequest :: PROPERTY_FIRST_NAME, '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(CasUserRequest :: PROPERTY_LAST_NAME, '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(CasUserRequest :: PROPERTY_EMAIL, '*' . $query . '*');
            $conditions[] = new OrCondition($query_conditions);
        }

        if (! $user->is_platform_admin())
        {
            $conditions[] = new EqualityCondition(CasUserRequest :: PROPERTY_REQUESTER_ID, $user->get_id());
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
                $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('RequestAccount'), Theme :: get_image_path() . 'action_request.png', $this->get_url(array(
                        Application :: PARAM_ACTION => CasUserManager :: ACTION_CREATE))));
                $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageAccounts'), Theme :: get_image_path() . 'action_manage.png', $this->get_url(array(
                        Application :: PARAM_ACTION => CasUserManager :: ACTION_ACCOUNT))));
            }
        }
        return $this->action_bar;
    }
}
?>