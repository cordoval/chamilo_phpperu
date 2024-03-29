<?php
namespace application\weblcms;

use common\libraries\Header;
use admin\AdminManager;
use common\libraries\Redirect;
use common\libraries\Display;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: course_importer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */

require_once dirname(__FILE__) . '/../../course/course_import_form.class.php';

/**
 * Weblcms component allows the use to import a course
 */
class WeblcmsManagerCourseImporterComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');

        $trail = BreadcrumbTrail :: get_instance();
        //        if ($this->get_user()->is_platform_admin())
        //        {
        //            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        //            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_IMPORTER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Importer')));
        //            //$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        //        }
        //        else
        //        {
        //        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
        //        }
        //        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseImportCSV')));
        //


        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get('NotAllowed', null ,Utilities:: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $form = new CourseImportForm(CourseImportForm :: TYPE_IMPORT, $this->get_url());

        if ($form->validate())
        {
            $success = $form->import_courses();
            $this->redirect(Translation :: get($success ? 'CsvCoursesProcessed' : 'CsvCoursesNotProcessed') . '<br />' . $form->get_failed_csv(), ($success ? false : true));
        }
        else
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
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
        $html[] = '<b>action</b>;<b>code</b>;<b>title</b>;<b>category</b>;<b>teacher</b>';
        $html[] = 'A;BIO0015;Biology;BIO;username';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . Translation :: get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation :: get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES );
        $html[] = '<br />U: ' . Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES );
        $html[] = '<br />D: ' . Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES );
        $html[] = '</blockquote>';

        echo implode($html, "\n");
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('TypeName', null, 'admin')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }

        $breadcrumbtrail->add_help('weblcms_course_importer');
    }

    function get_additional_parameters()
    {
        return array();
    }
}
?>