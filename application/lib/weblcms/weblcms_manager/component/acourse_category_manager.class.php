<?php
/**
 * $Id: acourse_category_manager.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/course_category_browser/course_category_browser_table.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/../../course/course_category_form.class.php';
require_once dirname(__FILE__) . '/../../course/course_category_menu.class.php';

/**
 * Weblcms component allows the user to manage course categories
 */
class WeblcmsManagerCourseCategoryManagerComponent extends WeblcmsManager
{
    private $category;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseCategoryManager')));
            $trail->add_help('courses category manager');
            
            $this->display_header($trail, false, true);
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->category = Request :: get(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID);
        
        $component_action = $this->get_parameter(WeblcmsManager :: PARAM_COMPONENT_ACTION);
        
        switch ($component_action)
        {
            case 'edit' :
                $this->edit_course_category();
                break;
            case 'delete' :
                $this->delete_course_category();
                break;
            case 'add' :
                $this->add_course_category();
                break;
            case 'view' :
                $this->show_course_category_list();
                break;
            default :
                $this->show_course_category_list();
        }
    }

    function show_course_category_list()
    {
        $this->display_page_header(Translation :: get('CourseCategoryManager'));
        $this->display_course_categories();
        $this->display_footer();
    }

    function display_page_header($title)
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), $title));
        $trail->add_help('courses category manager');
        $this->display_header($trail, false, true);
    }

    function display_course_categories()
    {
        echo $this->get_course_category_manager_modification_links();
        echo '<div style="clear: both;">&nbsp;</div>';
        echo $this->get_menu_html();
        echo $this->get_course_category_html();
    }

    function get_course_category_html()
    {
        $table = new CourseCategoryBrowserTable($this, null, null, $this->get_condition());
        
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    function get_menu_html()
    {
        $extra_items = array();
        if ($this->get_search_validate())
        {
            // $search_url = $this->get_url();
            $search_url = '#';
            $search = array();
            $search['title'] = Translation :: get('SearchResults');
            $search['url'] = $search_url;
            $search['class'] = 'search_results';
            $extra_items[] = $search;
        }
        else
        {
            $search_url = null;
        }
        
        $temp_replacement = '__CATEGORY_ID__';
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_COURSE_CATEGORY_MANAGER, WeblcmsManager :: PARAM_COURSE_CATEGORY_ID => $temp_replacement));
        $url_format = str_replace($temp_replacement, '%s', $url_format);
        $category_menu = new CourseCategoryMenu($this->category, $url_format);
        
        if (isset($search_url))
        {
            $category_menu->forceCurrentUrl($search_url, true);
        }
        
        $html = array();
        $html[] = '<div style="float: left; width: 20%;">';
        $html[] = $category_menu->render_as_tree();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    function add_course_category()
    {
        $coursecategory = new CourseCategory();
        
        $coursecategory->set_auth_cat_child(1);
        $coursecategory->set_auth_course_child(1);
        
        $form = new CourseCategoryForm(CourseCategoryForm :: TYPE_CREATE, $coursecategory, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_course_category();
            $this->redirect(Translation :: get($success ? 'CourseCategoryAdded' : 'CourseCategoryNotAdded'), ($success ? false : true));
        }
        else
        {
            $this->display_page_header(Translation :: get('CreateCourseCategory'));
            $form->display();
            echo '<h3>' . Translation :: get('CourseCategoryList') . '</h3>';
            $this->display_course_categories();
            $this->display_footer();
        }
    }

    function edit_course_category()
    {
        $course_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID);
        $course_category = $this->retrieve_course_category($course_category_id);
        
        $form = new CourseCategoryForm(CourseCategoryForm :: TYPE_EDIT, $course_category, $this->get_url(array(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID => $course_category_id)));
        
        if ($form->validate())
        {
            $success = $form->update_course_category();
            $this->redirect(Translation :: get($success ? 'CourseCategoryUpdated' : 'CourseCategoryNotUpdated'), ($success ? false : true), array(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID => $course_category_id));
        }
        else
        {
            $this->display_page_header(Translation :: get('UpdateCourseCategory'));
            $form->display();
            echo '<h3>' . Translation :: get('CourseCategoryList') . '</h3>';
            $this->display_course_categories();
            $this->display_footer();
        }
    }

    function delete_course_category()
    {
        $course_category_id = Request :: get(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID);
        $coursecategory = $this->retrieve_course_category($course_category_id);
        
        $success = $coursecategory->delete();
        $this->redirect(Translation :: get($success ? 'CourseCategoryDeleted' : 'CourseCategoryNotDeleted'), ($success ? false : true), array(WeblcmsManager :: PARAM_COMPONENT_ACTION => 'view'));
    }

    function get_course_category_manager_modification_links()
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->get_course_category_add_url(), 'label' => Translation :: get('CreateCourseCategory'), 'img' => Theme :: get_common_image_path() . 'action_create.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
        
        return Utilities :: build_toolbar($toolbar_data);
    }

    function get_condition()
    {
        $condition = null;
        if (isset($this->category))
        {
            $condition = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $this->category);
        }
        
        return $condition;
    }
}
?>