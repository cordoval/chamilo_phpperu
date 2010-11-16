<?php
namespace application\weblcms;

use common\libraries\Categorizable;
use common\libraries\PatternMatchCondition;
use repository\ContentObject;
use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTableFormActions;
use common\libraries\ActionBarRenderer;
use common\libraries\SubselectCondition;
use repository\RepositoryDataManager;
use common\libraries\ToolbarItem;
use user\User;
use common\libraries\Theme;
use common\libraries\OrCondition;
use common\libraries\InCondition;
use common\libraries\Utilities;
use common\libraries\ObjectTableOrder;
use common\libraries\AndCondition;
use common\libraries\InequalityCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Translation;

/**
 * $Id: viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

require_once dirname(__file__) . '/../../browser/content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../browser/content_object_publication_category_tree.class.php';

class ToolComponentBrowserComponent extends ToolComponent
{
    private $action_bar;
    private $introduction_text;
    private $publication_category_tree;
    private $publications;

    function run()
    {
        $this->introduction_text = $this->get_parent()->get_introduction_text();
        $this->action_bar = $this->get_action_bar();

        $tree_id = Request :: get(WeblcmsManager :: PARAM_CATEGORY);
        $this->publication_category_tree = new ContentObjectPublicationCategoryTree($this, $tree_id);

        $publication_renderer = ContentObjectPublicationListRenderer :: factory($this->get_parent()->get_browser_type(), $this);

        $actions = new ObjectTableFormActions(Tool :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(Tool :: ACTION_DELETE, Translation :: get('RemoveSelected', null ,Utilities:: COMMON_LIBRARIES)));
        $actions->add_form_action(new ObjectTableFormAction(Tool :: ACTION_TOGGLE_VISIBILITY, Translation :: get('ToggleVisibility'), false));
        //$actions->add_form_action(new ObjectTableFormAction(Tool :: ACTION_TOGGLE_VISIBILITY, Translation :: get('ToggleVisibility'), false));
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT) && $this->get_parent() instanceof Categorizable)
        {
            $actions->add_form_action(new ObjectTableFormAction(Tool :: ACTION_MOVE_TO_CATEGORY, Translation :: get('MoveSelected', null ,Utilities:: COMMON_LIBRARIES), false));
        }
        $publication_renderer->set_actions($actions);

        $this->display_header();

        if ($this->get_course()->get_intro_text())
        {
            echo $this->get_parent()->display_introduction_text($this->introduction_text);
        }

        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';

        if ($this->get_parent() instanceof Categorizable)
        {
            echo '<div style="width:15%; float: left; overflow: auto;">';
            echo $this->publication_category_tree->as_html();
            echo '</div>';
            echo '<div style="width:83%; padding-left: 1%; float:right; ">';
        }

        echo $publication_renderer->as_html();

        if ($this->get_parent() instanceof Categorizable)
        {
            echo '</div>';
        }
        echo '</div>';

        if (method_exists($this->get_parent(), 'show_additional_information'))
        {
            $this->get_parent()->show_additional_information($this);
        }

        $this->display_footer();
    }

    /**
     * Retrieves the publications
     * @return array An array of ContentObjectPublication objects
     */
    function get_publications($offset, $max_objects, ObjectTableOrder $object_table_order)
    {
        if (empty($this->publications))
        {
            $datamanager = WeblcmsDataManager :: get_instance();
            $condition = $this->get_publication_conditions();

            $this->publications = $datamanager->retrieve_content_object_publications($condition, $object_table_order, $offset, $max_objects)->as_array();
        }

        return $this->publications;

    }

    /**
     * Retrieves the number of published content objects
     * @return int
     */
    function get_publication_count()
    {
        return WeblcmsDataManager :: get_instance()->count_content_object_publications($this->get_publication_conditions());
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_rights.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_RIGHTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT) && $this->get_parent() instanceof Categorizable)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if (! $this->introduction_text && $this->get_course()->get_intro_text())
        {
            if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        if (method_exists($this->get_parent(), 'get_tool_actions'))
        {
            $action_bar->set_tool_actions($this->get_parent()->get_tool_actions());
        }

        $browser_types = $this->get_parent()->get_available_browser_types();

        if (count($browser_types) > 1)
        {
            foreach ($browser_types as $browser_type)
            {
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get(Utilities :: underscores_to_camelcase($browser_type) . 'View'), Theme :: get_image_path() . 'view_' . $browser_type . '.png', $this->get_url(array(Tool :: PARAM_BROWSER_TYPE => $browser_type)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        return $action_bar;
    }

    function get_publication_conditions()
    {
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
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
        $conditions[] = new InCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $this->publication_category_tree->get_current_category_id());

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
            $access[] = new AndCondition(array(
                    new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_USER, null, ContentObjectPublicationUser :: get_table_name()), new EqualityCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, null, ContentObjectPublicationCourseGroup :: get_table_name())));
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

        if (method_exists($this->get_parent(), 'get_tool_conditions'))
        {
            foreach ($this->get_parent()->get_tool_conditions() as $tool_condition)
            {
                $conditions[] = $tool_condition;
            }
        }

        if (! ($this->is_allowed(WeblcmsRights :: DELETE_RIGHT) || $this->is_allowed(WeblcmsRights :: EDIT_RIGHT)))
        {
            $time_conditions = array();
            $time_conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_HIDDEN, 0);

            $forever_condition = new AndCondition(array(new EqualityCondition(ContentObjectPublication :: PROPERTY_FROM_DATE, 0), new EqualityCondition(ContentObjectPublication :: PROPERTY_TO_DATE, 0)));
            $between_condition = new AndCondition(array(
                    new InequalityCondition(ContentObjectPublication :: PROPERTY_FROM_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time()), new InequalityCondition(ContentObjectPublication :: PROPERTY_TO_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time())));

            $time_conditions[] = new OrCondition(array($forever_condition, $between_condition));

            $conditions[] = new AndCondition($time_conditions);
        }

        return new AndCondition($conditions);
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

    function count_tool_categories()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, $this->get_tool_id());
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->get_course_id());
        $condition = new AndCondition($conditions);

        return WeblcmsDataManager :: get_instance()->count_content_object_publication_categories($condition);
    }

}
?>