<?php
namespace rights;

use common\libraries\ActionBarSearchForm;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Application;
use common\libraries\BasicApplication;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\ResourceManager;
use common\libraries\ObjectTableOrder;
use common\libraries\ActionBarRenderer;

use rights\RightsUtilities;
use user\UserDataManager;
/**
 * $Id: browser.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.user_right_manager.component
 */
require_once Path :: get_rights_path() . 'lib/user_right_manager/component/user_location_browser_table/user_location_browser_table.class.php';

class UserRightManagerBrowserComponent extends UserRightManager
{
    private $action_bar;

    private $application;
    private $location;
    private $user;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->application = Request :: get(UserRightManager :: PARAM_SOURCE);
        $location = Request :: get(UserRightManager :: PARAM_LOCATION);
        $user = Request :: get(UserRightManager :: PARAM_USER);

        if (! isset($user))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoUserSelected'));
            $this->display_footer();
            exit();
        }
        else
        {
            $udm = UserDataManager :: get_instance();
            $this->user = $udm->retrieve_user($user);
        }

        if (! isset($this->application))
        {
            $this->application = 'admin';
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, 0);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_TYPE, 'root');
        $condition = new AndCondition($conditions);
        $root = RightsDataManager :: get_instance()->retrieve_locations($condition, null, 1, array(
                new ObjectTableOrder(Location :: PROPERTY_LOCATION)))->next_result();

        if (isset($location))
        {
            $this->location = $this->retrieve_location($location);
        }
        else
        {
            $this->location = $root;
        }

        /*$parents = array_reverse($this->location->get_parents()->as_array());
        foreach ($parents as $parent)
        {
            $trail->add(new Breadcrumb($this->get_url(array('location' => $parent->get_id())), $parent->get_location()));
        }*/

        $this->action_bar = $this->get_action_bar();

        $this->display_header();

        $html = array();
        $application_url = $this->get_url(array(
                Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS,
                UserRightManager :: PARAM_USER => $this->user->get_id(),
                UserRightManager :: PARAM_SOURCE => Application :: PLACEHOLDER_APPLICATION));
        $html[] = BasicApplication :: get_selecter($application_url, $this->application);
        $html[] = $this->action_bar->as_html() . '<br />';

        $url_format = $this->get_url(array(
                Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS,
                UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_USER_RIGHTS,
                UserRightManager :: PARAM_USER => $this->user->get_id(),
                UserRightManager :: PARAM_SOURCE => $this->application,
                UserRightManager :: PARAM_LOCATION => '%s'));
        $url_format = str_replace('=%25s', '=%s', $url_format);
        $location_menu = new LocationMenu($root->get_id(), $this->location->get_id(), $url_format);
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $location_menu->render_as_tree();
        $html[] = '</div>';

        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new UserLocationBrowserTable($this, $parameters, $this->get_condition($location));

        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = RightsUtilities :: get_rights_legend();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'rights/javascript/configure_user.js');

        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_condition($location)
    {
        if (! $location)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, 0);
            $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);
            $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_TYPE, 'root');

            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new EqualityCondition(Location :: PROPERTY_PARENT, $this->location->get_id());

            $query = $this->action_bar->get_query();
            if (isset($query) && $query != '')
            {
                $and_conditions = array();
                $and_conditions[] = $condition;
                $and_conditions[] = new PatternMatchCondition(Location :: PROPERTY_LOCATION, '*' . $query . '*');
                $condition = new AndCondition($and_conditions);
            }
        }

        return $condition;
    }

    function get_source()
    {
        return $this->application;
    }

    function get_current_user()
    {
        return $this->user;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(
                UserRightManager :: PARAM_SOURCE => $this->application,
                UserRightManager :: PARAM_USER => $this->user->get_id(),
                UserRightManager :: PARAM_LOCATION => $this->location->get_id())));

        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('rights_users_browser');
    }

    function get_additional_parameters()
    {
        return array(
                UserRightManager :: PARAM_SOURCE,
                UserRightManager :: PARAM_LOCATION,
                UserRightManager :: PARAM_USER);
    }
}
?>