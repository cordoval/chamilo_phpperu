<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: subscribe_user_browser.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerSubscribeUserBrowserComponent extends GroupManager implements AdministrationComponent
{
    private $group;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $group_id = Request :: get(GroupManager :: PARAM_GROUP_ID);

        if (isset($group_id))
        {
            $this->group = $this->retrieve_group($group_id);
        }

        if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_SUBSCRIBE, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        $this->ab = $this->get_action_bar();
        $output = $this->get_user_subscribe_html();

        $this->display_header();
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_user_subscribe_html()
    {
        $parameters = $this->get_parameters();
        $parameters[GroupManager :: PARAM_GROUP_ID] = $this->group->get_id();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        
    	$table = new SubscribeUserBrowserTable($this, $parameters, $this->get_subscribe_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_subscribe_condition()
    {
        $condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, Request :: get(GroupRelUser :: PROPERTY_GROUP_ID));

        $users = $this->retrieve_group_rel_users($condition);

        $conditions = array();
        while ($user = $users->next_result())
        {
            $conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_ID, $user->get_user_id()));
        }

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }

        if (count($conditions) == 0)
            return null;

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_group()
    {
        return $this->group;
    }

    function get_action_bar()
    {
        $group = $this->group;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowGroup'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS, GroupManager :: PARAM_GROUP_ID => $group->get_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID))), Translation :: get('GroupManagerViewerComponent')));
    	$breadcrumbtrail->add_help('group general');
    }
    
    function get_additional_parameters()
    {
    	return array(GroupManager :: PARAM_GROUP_ID);
    }
}
?>