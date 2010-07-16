<?php
/**
 * $Id: sorter.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/../../course/course_user_relation_form.class.php';
require_once dirname(__FILE__) . '/../../course/course_user_category_form.class.php';
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
        $course = Request :: get(WeblcmsManager :: PARAM_COURSE_USER);
        $course_type_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);
        
        if (isset($direction) && isset($course) && isset($course_type_id))
        {
            $success = $this->move_course($course, $course_type_id, $direction);
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
        $category = Request :: get(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID);
        $course_type_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);
        
        if (isset($direction) && isset($category) && isset($course_type_id))
        {
            $success = $this->move_category($category, $course_type_id, $direction);
            $this->redirect(Translation :: get($success ? 'CourseUserCategoryMoved' : 'CourseUserCategoryNotMoved'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT));
        }
        else
        {
            $this->show_course_list();
        }
    }

    function assign_course_category()
    {
        $course_id = Request :: get(WeblcmsManager :: PARAM_COURSE_USER);
        $courseuserrelation = $this->retrieve_course_user_relation($course_id, $this->get_user_id());
        $form = new CourseUserRelationForm(CourseUserRelationForm :: TYPE_EDIT, $courseuserrelation, $this->get_user(), $this->get_url(array()));
        
        if ($form->validate())
        {
            $success = $form->update_course_user_relation();
            $this->redirect(Translation :: get($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT));
        }
        else
        {
            $this->display_page_header(Translation :: get('SetCourseUserCategory'));
            $form->display();
            //			echo '<h3>'. Translation :: get('UserCourseList') .'</h3>';
            //			$this->display_courses();
            $this->display_footer();
        }
    }

    function move_course($course, $course_type_id, $direction)
    {
        $move_courseuserrelation = $this->retrieve_course_user_relation($course, $this->get_user_id());
        $sort = $move_courseuserrelation->get_sort();
        $next_courseuserrelation = $this->retrieve_course_user_relation_at_sort($this->get_user_id(), $course_type_id, $move_courseuserrelation->get_category(), $sort, $direction);
        
        if ($direction == 'up')
        {
            $move_courseuserrelation->set_sort($sort - 1);
            $next_courseuserrelation->set_sort($sort);
        }
        elseif ($direction == 'down')
        {
            $move_courseuserrelation->set_sort($sort + 1);
            $next_courseuserrelation->set_sort($sort);
        }
        
        if ($move_courseuserrelation->update() && $next_courseuserrelation->update())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function move_category($courseusercategory, $course_type_id, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $courseusercategory);
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);
        $move_category = $this->retrieve_course_type_user_category($condition);
        $sort = $move_category->get_sort();
        $next_category = $this->retrieve_course_type_user_category_at_sort($this->get_user_id(), $course_type_id, $sort, $direction);
        
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
            //			echo '<h3>'. Translation :: get('UserCourseList') .'</h3>';
            //			$this->display_courses();
            $this->display_footer();
        }
    }

    function edit_course_user_category()
    {
        $course_user_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_USER_CATEGORY_ID);
        $condition = new EqualityCondition(CourseUserCategory :: PROPERTY_ID, $course_user_category_id);
        $courseusercategory = $this->retrieve_course_user_category($condition);
        
        $form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_EDIT, $courseusercategory, $this->get_user(), $this->get_url(array(WeblcmsManager :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category_id)), $this);
        
        if ($form->validate())
        {
            $success = $form->update_course_user_category();
            $this->redirect(Translation :: get($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => 'view', WeblcmsManager :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category_id));
        }
        else
        {
            $this->display_page_header(Translation :: get('EditCourseUserCategory'));
            $form->display();
            //			echo '<h3>'. Translation :: get('UserCourseList') .'</h3>';
            //			$this->display_courses();
            $this->display_footer();
        }
    }

    function delete_course_type_user_category()
    {
        $course_user_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_USER_CATEGORY_ID);
        $course_type_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);
        
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID, $course_user_category_id);
        $conditions[] = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
        $condition = new AndCondition($conditions);
        $course_type_user_category = $this->retrieve_course_type_user_category($condition);
        
        $success = $course_type_user_category->delete();
        $this->redirect(Translation :: get($success ? 'CourseUserCategoryDeleted' : 'CourseUserCategoryNotDeleted'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => 'view'));
    }

    function display_page_header($title)
    {
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(null, array(Application :: PARAM_ACTION)), Translation :: get('MyCourses')));
        //$trail->add(new Breadcrumb($this->get_url(null, array(WeblcmsManager :: PARAM_COURSE, WeblcmsManager :: PARAM_COMPONENT_ACTION, WeblcmsManager :: PARAM_ACTION)), Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($this->get_url(null, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT, WeblcmsManager :: PARAM_COMPONENT_ACTION, WeblcmsManager :: PARAM_COURSE)), Translation :: get('SortMyCourses')));
        $trail->add_help('courses general');
        if (! empty($title))
        {
            $trail->add(new Breadcrumb($this->get_url(array('category' => Request :: get('category'))), $title));
        }
        $this->display_header($trail, false, true);
        echo '<div class="clear"></div><br />';
    }
    
    /**
     * Shows the tabs of the course types
     * For each of the tabs show the course list
     */
    function show_course_list()
    {
    	$course_active_types = $this->retrieve_active_course_types();
    	$renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $course_tabs = new DynamicTabsRenderer($renderer_name);
        
        $index = 0;
        
        $course_tabs->add_tab(new DynamicContentTab($index, Translation :: get('NoCourseType'), null, $this->display_courses_for_course_type(0)));
        $index++;
        
        while ($course_type = $course_active_types->next_result())
        {
            $course_tabs->add_tab(new DynamicContentTab($index, $course_type->get_name(), null, $this->display_courses_for_course_type($course_type->get_id())));
            $index++;
        }
        
        $this->display_page_header();
        echo $this->get_actionbar()->as_html();
        echo '<div class="clear"></div><br />';
        echo $course_tabs->render();
        $this->display_footer();
    }
    
    /**
     * Displays the course list for a single course type
     * @param int $course_type_id
     */
    function display_courses_for_course_type($course_type_id)
    {
    	$html[] = $this->display_user_course_category(null);
    	
    	$course_categories = WeblcmsDataManager :: get_instance()->retrieve_course_user_categories_from_course_type($course_type_id, $this->get_user_id());
    	
    	$count = 0;
    	$size = $course_categories->size();
    	
    	while($course_category = $course_categories->next_result())
    	{
    		$html[] = $this->display_user_course_category($course_category, $course_type_id, $count, $size);
    		$count++;
    	}
    	
    	return implode($html, "\n");
    }
    
    /**
     * Displays the user course category box
     * @param CourseUserCategory $course_category
     * @param int $course_type_id
     * @param int $index
     * @param int $count
     */
    function display_user_course_category(CourseUserCategory $course_category, $course_type_id, $index, $count)
    {
    	if (isset($course_category))
        {
            $title = Utilities :: htmlentities($course_category->get_title());
            $links = $this->get_category_modification_links($course_category, $course_type_id, $index, $count);
            $course_category_id = $course_category->get_id();
        }
        else
        {
            $title = Translation :: get('GeneralCourses');
            $course_category_id = 0;
        }
        
        $html[] = '<div class="user_course_category">';
        $html[] = '<div class="title">';
        $html[] = $title;
        $html[] = '</div>';
        $html[] = '<div class="options">';
        $html[] = $links;
        $html[] = '</div>';
        $html[] = '<div style="clear:both;"></div>';
        $html[] = '</div>';
        
        $html[] = $this->display_courses_for_user_course_category($course_category_id, $course_type_id);
        
        return implode($html, "\n");
    }
    
    /**
     * Displays the courses for a user course category
     * @param int $course_category_id
     * @param int $course_type_id
     */
    function display_courses_for_user_course_category($course_category_id, $course_type_id)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, $course_category_id, CourseUserRelation :: get_table_name());
    	$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id(), CourseUserRelation :: get_table_name());
    	$condition = new AndCondition($conditions);
    	
    	$courses = WeblcmsDataManager :: get_instance()->retrieve_user_courses($condition);
    	$size = $courses->size();
    	
    	$html = array();
        
        if ($size > 0)
        {
            $html[] = '<div>';
            $count = 0;
            while($course = $courses->next_result())
            {
                $titular = UserDataManager :: get_instance()->retrieve_user($course->get_titular());
                $html[] = '<div class="user_course"><a href="' . $this->get_course_viewing_url($course) . '">' . $course->get_name() . '</a><br />' . $course->get_visual() . ' - ' . $titular->get_fullname() . '</div>';
                $html[] = '<div class="user_course_options">';
                $html[] = $this->get_course_modification_links($course, $course_type_id, $count, $size);
                $html[] = '</div>';
                $html[] = '<div style="clear:both;"></div>';
                $count++;
            }
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<div class="nocourses">' . Translation :: get('NoCourses') . '</div><br />';
        }
        
        return implode($html, "\n");
    }

    function get_actionbar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateCourseUserCategory'), Theme :: get_common_image_path() . 'action_create.png', $this->get_course_user_category_add_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }

    function get_course_modification_links($course, $course_type_id, $key, $total)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if ($key > 0 && $total > 1)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $this->get_course_user_move_url($course, $course_type_id, 'up'), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('Up'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($key < ($total - 1) && $total > 1)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down.png', $this->get_course_user_move_url($course, $course_type_id, 'down'), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $this->get_course_user_edit_url($course), ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(new ToolbarItem(null, Theme :: get_common_image_path() . 'spacer_tab.png', null, ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }

    function get_category_modification_links($courseusercategory, $course_type_id, $key, $total)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if ($key > 0 && $total > 1)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $this->get_course_user_category_move_url($courseusercategory, $course_type_id, 'up'), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($key < ($total - 1) && $total > 1)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down.png', $this->get_course_user_category_move_url($courseusercategory, $course_type_id, 'down'), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_course_user_category_edit_url($courseusercategory), ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_course_user_category_delete_url($courseusercategory, $course_type_id), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }
}
?>