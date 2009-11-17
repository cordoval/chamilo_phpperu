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
        if (! $this->create_default_categories_in_weblcms())
        {
            return false;
        }
        
        if (! $this->create_course())
        {
            return false;
        }
        
        return true;
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

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>