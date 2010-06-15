<?php
/**
 * $Id: viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

require_once dirname(__file__) . '/../../browser/content_object_publication_list_renderer.class.php';

class ToolBrowserComponent extends ToolComponent
{
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_action_bar();

        $this->display_header();

        $renderer = ContentObjectPublicationListRenderer :: factory(ContentObjectPublicationListRenderer :: TYPE_LIST, $this);

        $actions[] = new ObjectTableFormAction(Tool :: ACTION_DELETE, Translation :: get('DeleteSelected'));
        $actions[] = new ObjectTableFormAction(Tool :: ACTION_HIDE, Translation :: get('Hide'), false);
        $actions[] = new ObjectTableFormAction(Tool :: ACTION_SHOW, Translation :: get('Show'), false);

        $renderer->set_actions($actions);

        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $renderer->as_html();
        echo '</div>';

        $this->display_footer();
    }

    /**
     * Retrieves the publications
     * @return array An array of ContentObjectPublication objects
     */
    function get_publications($from, $count, $column, $direction)
    {
        if (empty($this->publications))
        {
            $datamanager = WeblcmsDataManager :: get_instance();
            if ($this->is_allowed(EDIT_RIGHT))
            {
                $user_id = array();
                $course_group_ids = array();
            }
            else
            {
                $user_id = $this->get_user_id();
                $course_groups = $this->get_course_groups();

                $course_group_ids = array();

                foreach ($course_groups as $course_group)
                {
                    $course_group_ids[] = $course_group->get_id();
                }
            }

            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $this->get_tool_id());

            /* $access = array();
            $access[] = new InCondition('user_id', $user_id, $datamanager->get_alias('content_object_publication_user'));
            $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_alias('content_object_publication_course_group'));
            if (! empty($user_id) || ! empty($course_groups))
            {
                $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_alias('content_object_publication_course_group'))));
            }*/

            $access = array();
            if ($user_id)
            {
                $access[] = new InCondition(ContentObjectPublicationUser :: PROPERTY_USER, $user_id, ContentObjectPublicationUser :: get_table_name());
            }

            if (count($course_group_ids) > 0)
            {
                $access[] = new InCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, $course_group_ids, ContentObjectPublicationCourseGroup :: get_table_name());
            }

            if (! empty($user_id) || ! empty($course_group_ids))
            {
                $access[] = new AndCondition(array(new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_USER, null, ContentObjectPublicationUser :: get_table_name()), new EqualityCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, null, ContentObjectPublicationCourseGroup :: get_table_name())));
            }

            $conditions[] = new OrCondition($access);

            $subselect_conditions = array();
            $subselect_conditions[] = new InCondition(ContentObject :: PROPERTY_TYPE, $this->get_allowed_types());
            if ($this->get_search_condition())
            {
                $subselect_conditions[] = $this->get_search_condition();
            }
            $subselect_condition = new AndCondition($subselect_conditions);

            $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());

            foreach ($this->get_parent()->get_tool_conditions() as $tool_condition)
            {
                $conditions[] = $tool_condition;
            }

            $condition = new AndCondition($conditions);

            $publications = $datamanager->retrieve_content_object_publications_new($condition, new ObjectTableOrder(Announcement :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_ASC));
            $visible_publications = array();
            while ($publication = $publications->next_result())
            {
                // If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
                if (! $publication->is_visible_for_target_users() && ! ($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
                {
                    continue;
                }
                $visible_publications[] = $publication;
            }
            $this->publications = $visible_publications;
        }

        return $this->publications;

    }

    /**
     * Retrieves the number of published content objects
     * @return int
     */
    function get_publication_count()
    {
        return count($this->get_publications());
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        if ($this->is_allowed(ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! $this->introduction_text && $this->get_course()->get_intro_text())
        {
            if ($this->is_allowed(EDIT_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        $action_bar->set_tool_actions($this->get_parent()->get_tool_actions());

        return $action_bar;
    }

    function get_search_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            return new OrCondition($conditions);
        }

        return null;
    }

}
?>