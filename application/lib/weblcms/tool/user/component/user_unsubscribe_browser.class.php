<?php
/**
 * $Id: user_unsubscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */
require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/../user_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../weblcms_manager/component/subscribed_user_browser/subscribed_user_browser_table.class.php';

class UserToolUnsubscribeBrowserComponent extends UserToolComponent
{
    private $action_bar;
    private $introduction_text;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'user');

        $subselect_condition = new EqualityCondition('type', 'introduction');
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();

        $this->action_bar = $this->get_action_bar();
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses user');

        $this->display_header($trail, true);

        //echo '<br /><a name="top"></a>';
        //echo $this->perform_requested_actions();
        if (PlatformSetting :: get('enable_introduction', 'weblcms'))
        {
            echo $this->display_introduction_text();
        }
        echo $this->action_bar->as_html();
        echo $this->get_user_unsubscribe_html();

        $this->display_footer();
    }

    function get_user_unsubscribe_html()
    {
        $table = new SubscribedUserBrowserTable($this, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $this->get_course()->get_id(), WeblcmsManager :: PARAM_TOOL => $this->get_tool_id(), UserTool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USERS, 'application' => 'weblcms'), $this->get_unsubscribe_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array('tool_action' => UserTool :: ACTION_UNSUBSCRIBE_USERS)));

        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeUsers'), Theme :: get_image_path() . 'action_subscribe_user.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeGroups'), Theme :: get_image_path() . 'action_subscribe_group.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUPS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            if (! $this->introduction_text)
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        return $action_bar;
    }

    function get_unsubscribe_condition()
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

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $query);
            $conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, $query);
            $conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $query);
            return new OrCondition($conditions);
        }
    }

    function display_introduction_text()
    {
        $html = array();

        $introduction_text = $this->introduction_text;

        if ($introduction_text)
        {

            $tb_data[] = array('href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);

            $tb_data[] = array('href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);

            $html[] = '<div class="content_object">';
            $html[] = '<div class="description">';
            $html[] = $introduction_text->get_content_object()->get_description();
            $html[] = '</div>';
            $html[] = Utilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '<br />';
        }

        return implode("\n", $html);
    }
}
?>