<?php

/**
 * $Id: user_subscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */
require_once dirname(__FILE__) . '/../user_tool.class.php';

class UserToolSubscribeBrowserComponent extends UserTool
{

    private $action_bar;

    function run()
    {
        if (!$this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $this->action_bar = $this->get_action_bar();
        $trail = BreadcrumbTrail :: get_instance();


        $this->display_header();

        //echo '<br /><a name="top"></a>';
        //echo $this->perform_requested_actions();
        echo $this->action_bar->as_html();
        echo $this->get_user_subscribe_html();

        $this->display_footer();
    }

    function get_user_subscribe_html()
    {
        $table = new SubscribedUserBrowserTable($this, $this->get_parameters(), $this->get_subscribe_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewUsers'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USER_BROWSER)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('RequestUser'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_REQUEST_SUBSCRIBE_USER)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function get_subscribe_condition()
    {
        $condition = null;

        $relation_conditions = array();
        $relation_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->get_course()->get_id());
        $relation_condition = new AndCondition($relation_conditions);

        $users = $this->get_parent()->retrieve_course_user_relations($relation_condition);

        $conditions = array();
        while ($user = $users->next_result())
        {
            $conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_ID, $user->get_user()));
        }

        $condition = new AndCondition($conditions);

        if ($this->get_condition())
        {
            $condition = new AndCondition($condition, $this->get_condition());
        }

        return $condition;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            return new OrCondition($conditions);
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool::ACTION_UNSUBSCRIBE_USER_BROWSER)), Translation :: get('UserToolUnsubscribeBrowserComponent')));

        $breadcrumbtrail->add_help('weblcms_user_subscribe_browser');
    }

}

?>