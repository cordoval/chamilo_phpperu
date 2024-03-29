<?php
namespace rights;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Application;
use common\libraries\BasicApplication;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\InequalityCondition;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\ResourceManager;
use common\libraries\ObjectTableOrder;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;

use rights\RightsUtilities;
use group\GroupDataManager;
/**
 * $Id: browser.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.group_right_manager.component
 */
require_once Path :: get_rights_path() . 'lib/group_right_manager/component/group_location_browser_table/group_location_browser_table.class.php';

class GroupRightManagerBrowserComponent extends GroupRightManager
{
    private $action_bar;

    private $application;
    private $location;
    private $group;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->application = Request :: get(GroupRightManager :: PARAM_SOURCE);
        $location = Request :: get(GroupRightManager :: PARAM_LOCATION);
        $group = Request :: get(GroupRightManager :: PARAM_GROUP);

        if (! isset($group))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoGroupSelected'));
            $this->display_footer();
            exit();
        }
        else
        {
            $gdm = GroupDataManager :: get_instance();
            $this->group = $gdm->retrieve_group($group);
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
        $root = RightsDataManager :: get_instance()->retrieve_locations($condition, null, 1, array(new ObjectTableOrder(Location :: PROPERTY_LOCATION)))->next_result();

        if (isset($location))
        {
            $this->location = $this->retrieve_location($location);
        }
        else
        {
            $this->location = $root;
        }

        //		$parent_conditions = array();
        //		$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $this->location->get_left_value());
        //		$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $this->location->get_right_value());
        //		$parent_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);
        //
        //		$parent_condition = new AndCondition($parent_conditions);
        //		$order = array(new ObjectTableOrder(Location :: PROPERTY_LEFT_VALUE));
        //
        //		$parents = $this->retrieve_locations($parent_condition, null, null, $order);
        //
        //		while($parent = $parents->next_result())
        //		{
        //			$trail->add(new Breadcrumb($this->get_url(array('location' => $parent->get_id())), $parent->get_location()));
        //		}


        $this->action_bar = $this->get_action_bar();

        $this->display_header();

        $html = array();
        $application_url = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP => $this->group->get_id(), GroupRightManager :: PARAM_SOURCE => Application :: PLACEHOLDER_APPLICATION));
        $html[] = BasicApplication :: get_selecter($application_url, $this->application);
        $html[] = $this->action_bar->as_html() . '<br />';

        $url_format = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP => $this->group->get_id(), GroupRightManager :: PARAM_SOURCE => $this->application, GroupRightManager :: PARAM_LOCATION => '%s'));
        $url_format = str_replace('=%25s', '=%s', $url_format);
        $location_menu = new LocationMenu($root->get_id(), $this->location->get_id(), $url_format);
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $location_menu->render_as_tree();
        $html[] = '</div>';

        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new GroupLocationBrowserTable($this, $parameters, $this->get_condition($location));

        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = RightsUtilities :: get_rights_legend();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'rights/javascript/configure_group.js');

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

    function get_current_group()
    {
        return $this->group;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(GroupRightManager :: PARAM_SOURCE => $this->application, GroupRightManager :: PARAM_GROUP => $this->group->get_id(), GroupRightManager :: PARAM_LOCATION => $this->location->get_id())));

        return $action_bar;
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('rights_groups_browser');
    }

	function get_additional_parameters()
    {
    	return array(GroupRightManager :: PARAM_SOURCE, GroupRightManager :: PARAM_LOCATION, GroupRightManager :: PARAM_GROUP);
    }
}
?>