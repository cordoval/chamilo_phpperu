<?php
/**
 * $Id: user_unsubscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */
require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/subscribed_user_browser/subscribed_user_browser_table.class.php';
require_once dirname(__FILE__) . '/../course_group_user_menu.class.php';

class UserToolUnsubscribeBrowserComponent extends UserTool
{
    private $action_bar;
    private $introduction_text;

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'user');

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($condition);
        $this->introduction_text = $publications->next_result();

        $this->action_bar = $this->get_action_bar();
        $trail = BreadcrumbTrail :: get_instance();
        

        $this->display_header();

        if ($this->get_course()->get_intro_text())
        {
            echo $this->display_introduction_text($this->introduction_text);
        }

        echo $this->action_bar->as_html();
        echo $this->display_users();

        $this->display_footer();

        
    }

    function display_users()
    {
        $has_subscribed_groups = $this->get_course()->has_subscribed_groups();
        $html = array();

        if ($has_subscribed_groups)
        {
            $html[] = '<div style="float: left; width: 14%; overflow: auto;">';
            $html[] = $this->get_course_group_menu();
            $html[] = '</div>';
            $html[] = '<div style="float: right; width: 84%;">';
        }

        $html[] = $this->get_user_unsubscribe_html();

        if ($has_subscribed_groups)
        {
            $html[] = '</div>';
            $html[] = '<div class="clear"></div>';
        }

        return implode("\n", $html);
    }

    function get_user_unsubscribe_html()
    {
    	$parameters = $this->get_parameters();
        $table = new SubscribedUserBrowserTable($this, $parameters, $this->get_unsubscribe_condition());
        return $table->as_html();
    }

    function get_course_group_menu()
    {
        $group = Request :: get(WeblcmsManager :: PARAM_GROUP);
        $course_group_user_menu = new CourseGroupUserMenu($this->get_course(), $group);
        return $course_group_user_menu->render_as_tree();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $parameters = array();

        $group_id = Request :: get(WeblcmsManager :: PARAM_GROUP);
        if (isset($group_id))
        {
            $parameters[WeblcmsManager :: PARAM_GROUP] = $group_id;
        }

        $action_bar->set_search_url($this->get_url($parameters));

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeUsers'), Theme :: get_image_path() . 'action_subscribe_user.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USER_BROWSER)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeGroups'), Theme :: get_image_path() . 'action_subscribe_group.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUP_BROWSER)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            if (! $this->introduction_text)
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        return $action_bar;
    }

    function get_unsubscribe_condition()
    {
        $group_id = Request :: get(WeblcmsManager :: PARAM_GROUP);

        if (isset($group_id))
        {
            $group = GroupDataManager :: get_instance()->retrieve_group($group_id);

            $users = $group->get_users(true, true);
            if(count($users) == 0)
            {
            	return new EqualityCondition(User :: PROPERTY_ID, 0);
            }
            
            $conditions = array();
            $conditions[] = new InCondition(User :: PROPERTY_ID, $users);

            if ($this->get_condition())
            {
                $conditions[] = $this->get_condition();
            }

            return new AndCondition($conditions);
        }
        else
        {
            $condition = null;

            $relation_conditions = array();
            $relation_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->get_course()->get_id());
            $relation_condition = new AndCondition($relation_conditions);

            $users = $this->get_parent()->retrieve_course_user_relations($relation_condition);

            $conditions = array();
            while ($user = $users->next_result())
            {
                $conditions[] = new EqualityCondition(User :: PROPERTY_ID, $user->get_user());
            }

            $condition = new OrCondition($conditions);

            if ($this->get_condition())
            {
                $condition = new AndCondition($condition, $this->get_condition());
            }
            return $condition;
        }
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

    function  add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_user_unsubscribe_browser');
    }
}
?>