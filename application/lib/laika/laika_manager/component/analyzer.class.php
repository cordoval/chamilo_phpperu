<?php
/**
 * $Id: analyzer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/laika_group_browser/laika_group_browser_table.class.php';

class LaikaManagerAnalyzerComponent extends LaikaManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_STATISTICS)), Translation :: get('ViewStatistics')));

        if (! LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, LaikaRights :: LOCATION_ANALYZER, 'laika_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $group_id = $this->get_group();

        if (isset($group_id) && $group_id != 0)
        {
            $group = GroupDataManager :: get_instance()->retrieve_group($group_id);
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_STATISTICS, LaikaManager :: PARAM_GROUP_ID => $group->get_id())), $group->get_name()));
        }

        $this->display_header($trail);
        echo $this->get_table_html();
        $this->display_footer();
    }

    function get_table_html()
    {
        $html = array();

        $table = new LaikaGroupBrowserTable($this, $this->get_table_parameters(), $this->get_condition());
        $html[] = $table->as_html();

        return implode("\n", $html);
    }

    function get_group()
    {
        return (isset($_GET[LaikaManager :: PARAM_GROUP_ID]) ? $_GET[LaikaManager :: PARAM_GROUP_ID] : 0);
    }

    function get_condition()
    {
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_group());

        //		$query = $this->ab->get_query();
        //		if(isset($query) && $query != '')
        //		{
        //			$or_conditions = array();
        //			$or_conditions[] = new PatternMatchCondition(Group :: PROPERTY_NAME, '*' . $query . '*');
        //			$or_conditions[] = new PatternMatchCondition(Group :: PROPERTY_DESCRIPTION, '*' . $query . '*');
        //			$or_condition = new OrCondition($or_conditions);
        //
        //			$and_conditions[] = array();
        //			$and_conditions = $condition;
        //			$and_conditions = $or_condition;
        //			$condition = new AndCondition($and_conditions);
        //		}


        return $condition;
    }

    function get_table_parameters()
    {
        $extra_parameters = array();
        $extra_parameters[LaikaManager :: PARAM_GROUP_ID] = $this->get_group();

        $parameters = $this->get_parameters();

        return array_merge($extra_parameters, $parameters);
    }

    function count_groups($condition = null)
    {
        return GroupDataManager :: get_instance()->count_groups($condition);
    }

    function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return GroupDataManager :: get_instance()->retrieve_groups($condition, $offset, $count, $order_property);
    }
}
?>