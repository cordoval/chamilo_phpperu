<?php
/**
 * $Id: user_group_subscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */
require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/../user_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../weblcms_manager/component/subscribe_group_browser/subscribe_group_browser_table.class.php';

class UserToolGroupSubscribeBrowserComponent extends UserToolComponent
{
    private $action_bar;
    private $group;
    private $root_group;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $this->action_bar = $this->get_action_bar();
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUPS)), Translation :: get('SubscribeGroups')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SubscribeGroups')));
        $trail->add_help('courses user');

        $this->add_group_menu_breadcrumbs($trail);
        $this->display_header($trail, true);

        echo $this->action_bar->as_html();
        echo $this->get_group_menu();
        echo $this->get_group_subscribe_html();

        $this->display_footer();
    }

    function get_group_subscribe_html()
    {
        $table = new SubscribeGroupBrowserTable($this, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $this->get_course()->get_id(), WeblcmsManager :: PARAM_TOOL => 'user', UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUPS, 'application' => 'weblcms'), $this->get_condition());

        $html = array();
        $html[] = '<div style="width: 75%; float: right;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_group_menu()
    {
        $groupmenu = new GroupMenu($this->get_course(), Request :: get('group_id'), '?application=weblcms&go=courseviewer&course=' . $this->get_course()->get_id() . '&tool=user&tool_action=subscribe_groups&group_id=%s');
        return '<div style="overflow: auto; width: 20%; float: left;">' . $groupmenu->render_as_tree() . '<br /></div>';
    }

    private function add_group_menu_breadcrumbs(&$breadcrumb_trail)
    {
        $groupmenu = new GroupMenu($this->get_course(), Request :: get('group_id'), '?application=weblcms&go=courseviewer&course=' . $this->get_course()->get_id() . '&tool=user&tool_action=subscribe_groups&group_id=%s');
        foreach ($groupmenu->get_breadcrumbs() as $breadcrumb)
        {
            $breadcrumb_trail->add(new Breadcrumb($breadcrumb['url'], $breadcrumb['title']));
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS)));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewUsers'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USERS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function get_group()
    {
        if(!$this->group)
        {
    		$this->group = Request :: get(WeblcmsManager :: PARAM_GROUP);

    		if(!$this->group)
    		{
    			$this->group = $this->get_root_group()->get_id();
    		}

        }

        return $this->group;
    }

    function get_root_group()
    {
    	if(!$this->root_group)
    	{
    		$group = GroupDataManager :: get_instance()->retrieve_groups(new EqualityCondition(Group :: PROPERTY_PARENT, 0))->next_result();
    		$this->root_group = $group;
    	}

    	return $this->root_group;
    }

    function get_condition()
    {	
        $conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_group());

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions2[] = new PatternMatchCondition(Group :: PROPERTY_NAME, '*' . $query . '*');
            $conditions2[] = new PatternMatchCondition(Group :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $conditions[] = new OrCondition($conditions2);
        }

        return new AndCondition($conditions);
    }
}
?>