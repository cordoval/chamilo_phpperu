<?php
namespace application\weblcms\tool\course_settings;

use common\libraries\Translation;

/**
 * $Id: course_settings_updater.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_settings.component
 */
require_once dirname(__FILE__) . '/../../../course/course_form.class.php';

class CourseSettingsToolUpdaterComponent extends CourseSettingsTool
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses settings');
        
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $course_type_id = Request :: get('course_type');
        
        if (! is_null($course_type_id))
        {
            $parameters['course_type'] = $course_type_id;
        }
        
        $url = $this->get_url($parameters);
        $form = new CourseForm(CourseForm :: TYPE_EDIT, $this->get_course(), $this->get_user(), $url, $this);
        
        if ($form->validate())
        {
            $success = $form->update();
            $this->redirect(Translation :: get($success ? 'CourseSettingsUpdated' : 'CourseSettingsUpdateFailed'), ($success ? false : true), array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $this->get_course_id()), array(
                    WeblcmsManager :: PARAM_TOOL, WeblcmsManager :: PARAM_TOOL_ACTION));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
}
?>