<?php
/**
 * @package application.lib.weblcms.install
 */
require_once dirname(__FILE__) . '/../weblcms_manager/weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

require_once 'Tree/Tree.php';

/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class WeblcmsInstaller extends Installer
{

    /**
     * Constructor
     */
    function WeblcmsInstaller($values)
    {
        parent :: __construct($values, WeblcmsDataManager :: get_instance());
    }

    /**
     * Runs the install-script.
     */
    function install_extra()
    {
    	if (! $this->create_courses_subtree())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('CoursesTreeCreated'));
        }
        
    	if (! $this->create_default_categories_in_weblcms())
        {
            return false;
        }
        
        if (! $this->create_course_types())
        {
            return false;
        }
        
        /*if (! $this->create_course())
        {
            return false;
        }*/
        
        return true;
    }

	private function create_courses_subtree()
    {
    	return RightsUtilities :: create_subtree_root_location(WeblcmsManager :: APPLICATION_NAME, 0, 'courses_tree');
    }
    
    function create_default_categories_in_weblcms()
    {
        $application = $this->get_application();
        
        //Creating Language Skills
        $cat = new CourseCategory();
        $cat->set_name('Language skills');
        $cat->set_parent('0');
        $cat->set_display_order(1);
        
        if (! $cat->create())
        {
            return false;
        }
        
        //creating PC Skills
        $cat = new CourseCategory();
        $cat->set_name('PC skills');
        $cat->set_parent('0');
        $cat->set_display_order(1);
        if (! $cat->create())
        {
            return false;
        }
        
        //creating Projects
        $cat = new CourseCategory();
        $cat->set_name('Projects');
        $cat->set_parent('0');
        $cat->set_display_order(1);
        if (! $cat->create())
        {
            return false;
        }
        
        return true;
    }

    function create_course()
    {
        $course = new Course();
        $course->set_name('ExampleCourse');
        $course->set_titular(2);
        $course->set_category(1);
        $course->set_visual('EX');
        $course->set_language('english');
        $succes = $course->create();
        
        $wdm = WeblcmsDataManager :: get_instance();
        $succes &= $wdm->subscribe_user_to_course($course, '1', '1', 2);
        
        return $succes;
    }

    function create_course_types()
    {
    	$course_type = new CourseType();
    	$course_type->set_name('Type Zero');
    	$course_type->set_description('An ice cold course type.');
    	$succes = $course_type->create();
    	
    	$course_type_settings = new CourseTypeSettings();
    	$course_type_settings->set_course_type_id(1);
    	$course_type_settings->set_language('français');
    	$course_type_settings->set_language_fixed(1);
    	$course_type_settings->set_visibility(0);
    	$course_type_settings->set_visibility_fixed(1);
    	$course_type_settings->set_access(1);
    	$course_type_settings->set_access_fixed(0);
    	$course_type_settings->set_max_number_of_members(0);
    	$course_type_settings->set_max_number_of_members_fixed(1);
    	$succes = $succes && $course_type_settings->create();
    	
    	$course_type_layout = new CourseTypeLayout();
    	$course_type_layout->set_course_type_id(1);
    	$course_type_layout->set_intro_text(1);
    	$course_type_layout->set_intro_text_fixed(1);
    	$course_type_layout->set_student_view(1);
    	$course_type_layout->set_student_view_fixed(1);
    	$course_type_layout->set_layout(1);
    	$course_type_layout->set_layout_fixed(1);
    	$course_type_layout->set_tool_shortcut(1);
    	$course_type_layout->set_tool_shortcut_fixed(1);
    	$course_type_layout->set_menu(1);
    	$course_type_layout->set_menu_fixed(1);
    	$course_type_layout->set_feedback(1);
    	$course_type_layout->set_feedback_fixed(1);
    	$course_type_layout->set_course_code_visible(1);
    	$course_type_layout->set_course_code_visible_fixed(1);
    	$course_type_layout->set_course_manager_name_visible(1);
    	$course_type_layout->set_course_manager_name_visible_fixed(1);
    	$course_type_layout->set_course_languages_visible(1);
    	$course_type_layout->set_course_languages_visible_fixed(1);
    	$succes = $succes && $course_type_layout->create();
    	
    	$course_type = new CourseType();
    	$course_type->set_name('Gundanium Aloid');
    	$course_type->set_description('A type made of the strongest material known in fiction.');
    	$succes = $succes && $course_type->create();
    	
    	$course_type_settings = new CourseTypeSettings();
    	$course_type_settings->set_course_type_id(2);
    	$course_type_settings->set_language('english');
    	$course_type_settings->set_language_fixed(1);
    	$course_type_settings->set_visibility(1);
    	$course_type_settings->set_visibility_fixed(0);
    	$course_type_settings->set_access(1);
    	$course_type_settings->set_access_fixed(0);
    	$course_type_settings->set_max_number_of_members(30);
    	$course_type_settings->set_max_number_of_members_fixed(0);
    	$succes = $succes && $course_type_settings->create();
    	
    	$course_type_layout = new CourseTypeLayout();
    	$course_type_layout->set_course_type_id(2);
    	$course_type_layout->set_intro_text(1);
    	$course_type_layout->set_intro_text_fixed(1);
    	$course_type_layout->set_student_view(1);
    	$course_type_layout->set_student_view_fixed(1);
    	$course_type_layout->set_layout(1);
    	$course_type_layout->set_layout_fixed(1);
    	$course_type_layout->set_tool_shortcut(1);
    	$course_type_layout->set_tool_shortcut_fixed(1);
    	$course_type_layout->set_menu(1);
    	$course_type_layout->set_menu_fixed(1);
    	$course_type_layout->set_feedback(1);
    	$course_type_layout->set_feedback_fixed(1);
    	$course_type_layout->set_course_code_visible(1);
    	$course_type_layout->set_course_code_visible_fixed(1);
    	$course_type_layout->set_course_manager_name_visible(1);
    	$course_type_layout->set_course_manager_name_visible_fixed(1);
    	$course_type_layout->set_course_languages_visible(1);
    	$course_type_layout->set_course_languages_visible_fixed(1);
    	$succes = $succes && $course_type_layout->create();
    	
    	return $succes;
    }
    
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>