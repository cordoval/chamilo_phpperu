<?php
namespace application\weblcms;

use common\libraries\Display;
use common\libraries\Application;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: activity_changer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package weblcms.lib.weblcms_manager.component
 */

/**
 * Component for change of activity
 */
class WeblcmsManagerActivityChangerComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('course_type general');

        $type = Request :: get(WeblcmsManager :: PARAM_TYPE);
        $course_type_ids = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);

        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed', null ,Utilities:: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        //else
        if (($type == 'course_type' && $course_type_ids) || ($type == 'all'))
        {
            $this->change_course_type_activity($course_type_ids);
        }

        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoObjectsSelected', null ,Utilities:: COMMON_LIBRARIES));
            $this->display_footer();
        }

    }

    /**
     * Function to change the activity of course_types
     * @param Array of course_type_ids
     */
    function change_course_type_activity($course_type_ids)
    {
        if ($course_type_ids)
        {
            if (! is_array($course_type_ids))
            {
                $course_type_ids = array($course_type_ids);
            }

            $success = true;

            foreach ($course_type_ids as $course_type_id)
            {
                $course_type = $this->retrieve_course_type($course_type_id);
                if (Request :: get('extra'))
                {
                    $course_type->set_active(Request :: get('extra') == 'enable' ? 1 : 0);
                }
                else
                    $course_type->set_active(! $course_type->get_active());

                if (! $course_type->update())
                    $success = false;
            }

            $this->redirect(Translation :: get($success ? 'CourseTypeUpdated' : 'CourseTypeNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER));
        }
    }

}
?>