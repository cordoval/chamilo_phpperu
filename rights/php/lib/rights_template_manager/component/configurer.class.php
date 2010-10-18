<?php
namespace rights;

use common\libraries\Path;
use common\libraries\Application;
use common\libraries\BasicApplication;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\InequalityCondition;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\ResourceManager;

use rights\RightsUtilities;
/**
 * $Id: configurer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */
require_once Path :: get_rights_path() . 'lib/rights_template_manager/component/rights_template_location_browser_table/rights_template_location_browser_table.class.php';

class RightsTemplateManagerConfigurerComponent extends RightsTemplateManager
{
    private $action_bar;

    private $application;
    private $location;
    private $rights_template;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->application = Request :: get(RightsTemplateManager :: PARAM_SOURCE);
        $location = Request :: get(RightsTemplateManager :: PARAM_LOCATION);
        $rights_template = Request :: get(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);

        if (! isset($rights_template))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoUserSelected'));
            $this->display_footer();
            exit();
        }
        else
        {
            $this->rights_template = $this->retrieve_rights_template($rights_template);
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
        $application_url = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID => $this->rights_template->get_id(), RightsTemplateManager :: PARAM_SOURCE => Application :: PLACEHOLDER_APPLICATION));
        $html[] = BasicApplication :: get_selecter($application_url, $this->application);
        $html[] = $this->action_bar->as_html() . '<br />';

        $url_format = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID => $this->rights_template->get_id(), RightsTemplateManager :: PARAM_SOURCE => $this->application, RightsTemplateManager :: PARAM_LOCATION => '%s'));
        $url_format = str_replace('=%25s', '=%s', $url_format);
        $location_menu = new LocationMenu($root->get_id(), $this->location->get_id(), $url_format);
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $location_menu->render_as_tree();
        $html[] = '</div>';

        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new RightsTemplateLocationBrowserTable($this, $parameters, $this->get_condition($location));

        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = RightsUtilities :: get_rights_legend();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'rights/javascript/configure_rights_template.js');

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

    function get_current_rights_template()
    {
        return $this->rights_template;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(RightsTemplateManager :: PARAM_SOURCE => $this->application, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID => $this->rights_template->get_id(), RightsTemplateManager :: PARAM_LOCATION => $this->location->get_id())));

        return $action_bar;
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES,
    															  RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES)),
    										 Translation :: get('RightsTemplateManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('rights_templates_configurer');
    }

	function get_additional_parameters()
    {
    	return array(RightsTemplateManager :: PARAM_SOURCE, RightsTemplateManager :: PARAM_LOCATION, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);
    }
}
?>