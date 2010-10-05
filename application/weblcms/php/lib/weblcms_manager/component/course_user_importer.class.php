<?php
/**
 * $Id: course_user_importer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/../../course/course_user_import_form.class.php';

/**
 * Weblcms component allows the use to import course user relations
 */
class WeblcmsManagerCourseUserImporterComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
    	
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $form = new CourseUserImportForm(CourseUserImportForm :: TYPE_IMPORT, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->import_course_users();
            $this->redirect(Translation :: get($success ? 'CsvUsersProcessed' : 'CsvUsersNotProcessed') . '<br />' . $form->get_failed_csv(), ($success ? false : true));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_extra_information();
            $this->display_footer();
        }
    }

    function display_extra_information()
    {
        $html = array();
        $html[] = '<p>' . Translation :: get('CSVMustLookLike') . ' (' . Translation :: get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '<b>action</b>;<b>username</b>;<b>coursecode</b>;<b>status</b>';
        $html[] = 'A;jdoe;course01;1';
        $html[] = 'D;a.dam;course01;5';
        $html[] = '</pre></blockquote>';
        $html[] = '<p>' . Translation :: get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation :: get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation :: get('Add');
        $html[] = '<br />U: ' . Translation :: get('Update');
        $html[] = '<br />D: ' . Translation :: get('Delete');
        $html[] = '<br /><br />';
        $html[] = '<u><b>' . Translation :: get('Status') . '</u></b>';
        $html[] = '<br />1: ' . Translation :: get('Teacher');
        $html[] = '<br />5: ' . Translation :: get('Student');
        $html[] = '</blockquote>';
        
        echo implode($html, "\n");
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }

        $breadcrumbtrail->add_help('weblcms_course_user_importer');
    }

    function get_additional_parameters()
    {
    	return array();
    }
}
?>