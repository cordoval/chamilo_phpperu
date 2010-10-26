<?php
namespace application\weblcms;

use common\libraries\Translation;

/**
 * $Id: sorter.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../../course_type/course_type_user_category_rel_course_form.class.php';
require_once dirname(__FILE__) . '/../../course/course_user_category_form.class.php';
require_once dirname(__FILE__) . '/../../course/course_list_renderer/course_type_course_list_renderer.class.php';
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class WeblcmsManagerSorterComponent extends WeblcmsManager
{
    private $category;
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $component_action = Request :: get(WeblcmsManager :: PARAM_COMPONENT_ACTION);
        $this->set_parameter(WeblcmsManager :: PARAM_COMPONENT_ACTION, $component_action);

        switch ($component_action)
        {
            case 'add' :
                $this->add_course_user_category();
                break;
            case 'move' :
                $this->move_course_list();
                break;
            case 'movecat' :
                $this->move_category_list();
                break;
            case 'assign' :
                $this->assign_course_category();
                break;
            case 'edit' :
                $this->edit_course_user_category();
                break;
            case 'delete' :
                $this->delete_course_type_user_category();
                break;
            case 'view' :
                $this->show_course_list();
                break;
            default :
                $this->show_course_list();
        }
    }

    function move_course_list()
    {
        $direction = Request :: get(WeblcmsManager :: PARAM_DIRECTION);
        $course = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $course_type_user_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE_USER_CATEGORY_ID);

        if (isset($direction) && isset($course))
        {
            $success = $this->move_course($course, $course_type_user_category_id, $direction);
            $this->redirect(Translation :: get($success ? 'CourseUserMoved' : 'CourseUserNotMoved'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT));
        }
        else
        {
            $this->show_course_list();
        }
    }

    function move_category_list()
    {
        $direction = Request :: get(WeblcmsManager :: PARAM_DIRECTION);
        $course_type_user_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE_USER_CATEGORY_ID);

        if (isset($direction) && isset($course_type_user_category_id))
        {
            $success = $this->move_category($course_type_user_category_id, $direction);
            $this->redirect(Translation :: get($success ? 'CourseUserCategoryMoved' : 'CourseUserCategoryNotMoved'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT));
        }
        else
        {
            $this->show_course_list();
        }
    }

    function assign_course_category()
    {
        $course_id = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $course_type_user_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE_USER_CATEGORY_ID);

        if(!$course_type_user_category_id)
        {
        	$course_type_user_category_rel_course = new CourseTypeUserCategoryRelCourse();
        	$course_type_user_category_rel_course->set_course_id($course_id);
        	$course_type_user_category_rel_course->set_user_id($this->get_user_id());
        }
        else
        {
        	$conditions = array();
        	$conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_ID, $course_id);
        	$conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_id);
        	$condition = new AndCondition($conditions);

        	$course_type_user_category_rel_course = WeblcmsDataManager :: get_instance()->retrieve_course_type_user_category_rel_courses($condition)->next_result();
        }

        $form = new CourseTypeUserCategoryRelCourseForm( $course_type_user_category_rel_course, $this->get_user(),
        	$this->get_url(array(WeblcmsManager :: PARAM_COURSE => $course_id, WeblcmsManager :: PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category_id)));

        if ($form->validate())
        {
            $success = $form->update_course_type_user_category_rel_course();
            $this->redirect(Translation :: get($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true),
            	array(WeblcmsManager :: PARAM_COMPONENT_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT));
        }
        else
        {
            $this->display_page_header(Translation :: get('SetCourseUserCategory'));
            $form->display();
            $this->display_footer();
        }
    }

    function move_course($course_id, $course_type_user_category_id, $direction)
    {
		$conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_ID, $course_id);
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_id);
        $condition = new AndCondition($conditions);

        $course_type_user_category_rel_course = WeblcmsDataManager :: get_instance()->retrieve_course_type_user_category_rel_courses($condition)->next_result();

        $sort_factor = $direction == 'up' ? -1 : 1;
		$sort = $course_type_user_category_rel_course->get_sort();
		$new_sort = $sort + $sort_factor;

        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_id);
        $conditions[] = new EqualityCondition(CourseTypeUserCategoryRelCourse :: PROPERTY_SORT, $new_sort);
        $condition = new AndCondition($conditions);

        $next_course_type_user_category_rel_course = WeblcmsDataManager :: get_instance()->retrieve_course_type_user_category_rel_courses($condition)->next_result();

        $course_type_user_category_rel_course->set_sort($new_sort);
        $next_course_type_user_category_rel_course->set_sort($sort);

        if ($course_type_user_category_rel_course->update() && $next_course_type_user_category_rel_course->update())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function move_category($course_type_user_category_id, $direction)
    {
        $condition = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_ID, $course_type_user_category_id);
        $move_category = $this->retrieve_course_type_user_category($condition);
        $sort = $move_category->get_sort();
        $next_category = $this->retrieve_course_type_user_category_at_sort($this->get_user_id(), $move_category->get_course_type_id(), $sort, $direction);

        if ($direction == 'up')
        {
            $move_category->set_sort($sort - 1);
            $next_category->set_sort($sort);
        }
        elseif ($direction == 'down')
        {
            $move_category->set_sort($sort + 1);
            $next_category->set_sort($sort);
        }

        if ($move_category->update() && $next_category->update())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function add_course_user_category()
    {
        $courseusercategory = new CourseUserCategory();

        $form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_CREATE, $courseusercategory, $this->get_user(), $this->get_url(), $this);

        if ($form->validate())
        {
            $success = $form->create_course_user_category();
            $this->redirect(Translation :: get($success ? 'CourseUserCategoryAdded' : 'CourseUserCategoryNotAdded'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => 'view'));
        }
        else
        {
            $this->display_page_header(Translation :: get('CreateCourseUserCategory'));
            $form->display();
            $this->display_footer();
        }
    }

    function edit_course_user_category()
    {
        $course_type_user_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE_USER_CATEGORY_ID);
        $condition = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_ID, $course_type_user_category_id);
        $course_type_user_category = $this->retrieve_course_type_user_category($condition);

        $condition = new EqualityCondition(CourseUserCategory :: PROPERTY_ID, $course_type_user_category->get_course_user_category_id());
        $course_user_category = $this->retrieve_course_user_category($condition);

        $form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_EDIT, $course_user_category, $this->get_user(), $this->get_url(array(WeblcmsManager :: PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category_id)), $this);

        if ($form->validate())
        {
            $success = $form->update_course_user_category();
            $this->redirect(Translation :: get($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => 'view'));
        }
        else
        {
            $this->display_page_header(Translation :: get('EditCourseUserCategory'));
            $form->display();
            $this->display_footer();
        }
    }

    function delete_course_type_user_category()
    {
        $course_type_user_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE_USER_CATEGORY_ID);

        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_ID, $course_type_user_category_id);
        $condition = new AndCondition($conditions);
        $course_type_user_category = $this->retrieve_course_type_user_category($condition);

        $success = $course_type_user_category->delete();
        $this->redirect(Translation :: get($success ? 'CourseUserCategoryDeleted' : 'CourseUserCategoryNotDeleted'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => 'view'));
    }

    function display_page_header($title)
    {
        $trail = BreadcrumbTrail :: get_instance();

        $this->display_header($trail, false, true);
        echo '<div class="clear"></div><br />';
    }

    /**
     * Shows the tabs of the course types
     * For each of the tabs show the course list
     */
    function show_course_list()
    {
    	$renderer = new CourseTypeCourseListRenderer($this);

        $this->display_page_header();
        echo $this->get_actionbar()->as_html();
        echo '<div class="clear"></div><br />';
        $renderer->render();
        $this->display_footer();
    }

    function get_actionbar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateCourseUserCategory'), Theme :: get_common_image_path() . 'action_create.png', $this->get_course_user_category_add_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }

    function get_course_actions(CourseTypeUserCategory $course_type_user_category, $course, $course_type_id, $offset, $count)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if($course_type_user_category)
        {
	        if ($offset > 0 && $count > 1)
	        {
	            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $this->get_course_user_move_url($course_type_user_category, $course, 'up'), ToolbarItem :: DISPLAY_ICON));
	        }
	        else
	        {
	            $toolbar_data[] = array('label' => Translation :: get('Up'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
	            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem :: DISPLAY_ICON));
	        }

	        if ($offset < ($count - 1) && $count > 1)
	        {
	            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down.png', $this->get_course_user_move_url($course_type_user_category, $course, 'down'), ToolbarItem :: DISPLAY_ICON));
	        }
	        else
	        {
	            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem :: DISPLAY_ICON));
	        }
        }
        $toolbar->add_item(new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $this->get_course_user_edit_url($course_type_user_category, $course), ToolbarItem :: DISPLAY_ICON));

        return $toolbar->as_html();
    }

    function get_course_type_user_category_actions(CourseTypeUserCategory $course_type_user_category, $course_type_id, $offset, $count)
    {
        if(!$course_type_user_category)
        {
        	return;
        }

    	$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($offset > 0 && $count > 1)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $this->get_course_user_category_move_url($course_type_user_category, 'up'), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }

        if ($offset < ($count - 1) && $count > 1)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down.png', $this->get_course_user_category_move_url($course_type_user_category, 'down'), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }

        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_course_user_category_edit_url($course_type_user_category), ToolbarItem :: DISPLAY_ICON));

        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_course_user_category_delete_url($course_type_user_category), ToolbarItem :: DISPLAY_ICON, true));

        return '<div style="float:right;">' . $toolbar->as_html() . '</div>';
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_sorter');
    }

    function get_additional_parameters()
    {
    	return array();
    }
}
?>