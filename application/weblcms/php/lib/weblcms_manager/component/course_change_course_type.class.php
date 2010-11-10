<?php
namespace application\weblcms;

use common\libraries\Header;
use admin\AdminManager;
use common\libraries\Redirect;
use common\libraries\Display;
use common\libraries\Application;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: course_change_course_type.class.php 224 2010-04-06 14:40:30Z Yannick $
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../../course/course_change_course_type_form.class.php';

class WeblcmsManagerCourseChangeCourseTypeComponent extends WeblcmsManager
{

    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course_codes = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $course_type_code = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);

        if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }

        $trail = BreadcrumbTrail :: get_instance();

        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES ));
            $this->display_footer();
            exit();
        }

        $failures = 0;

        $course = $this->retrieve_courses(new EqualityCondition(COURSE :: PROPERTY_ID, Request :: get(WeblcmsManager :: PARAM_COURSE)))->next_result();

        $this->form = new CourseChangeCourseTypeForm($this->get_url(array(WeblcmsManager :: PARAM_COURSE => $course_codes)), $course, $this->get_user());

        if ($this->form->validate())
        {
            if (! empty($course_codes))
            {
                if (! is_array($course_codes))
                {
                    $course_codes = array($course_codes);
                }

                foreach ($course_codes as $course_code)
                {
                    if (! $this->move_course($course_code))
                    {
                        $failures ++;
                    }
                }

                if ($failures)
                {
                    if (count($course_codes) == 1)
                    {
                        $message = 'SelectedCourseCourseTypeChanged';
                    }
                    else
                    {
                        $message = 'SelectedCoursesCourseTypeNotChanged';
                    }
                }
                else
                {
                    if (count($course_codes) == 1)
                    {
                        $message = 'SelectedCourseCourseTypeChanged';
                    }
                    else
                    {
                        $message = 'SelectedCoursesCourseTypeChanged';
                    }
                }
                $parent = $this->form->get_new_parent();
                $this->redirect(! $failures ? Translation :: get($message) : Translation :: get($message), ! $failures ? (false) : true, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER, WeblcmsManager :: PARAM_COURSE_TYPE => $parent), array(
                        WeblcmsManager :: PARAM_COURSE, WeblcmsManager :: PARAM_COURSE_TYPE));
            }
            else
            {
                $this->display_error_page(htmlentities(Translation :: get('NoCourseSelected')));
            }
        }
        else
        {
            $this->display_header();
            $this->form->display();
            $this->display_footer();
        }
    }

    function move_course($course_code)
    {
        $new_course_type = $this->form->get_selected_course_type();
        $wdm = WeblcmsDataManager :: get_instance();
        $course_type = $wdm->retrieve_course_type($new_course_type);
        $course = $wdm->retrieve_course($course_code);
        return $course->update_by_course_type($course_type);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration', null, Utilities :: COMMON_LIBRARIES )));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
            $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER), array(WeblcmsManager :: PARAM_COURSE)), Translation :: get('CourseList')));
        }
        else
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courselist')));
        }

        $breadcrumbtrail->add_help('weblcms_change_course_type');
    }

    function get_additional_parameters()
    {
        return array();
    }

}

?>