<?php
/**
 * $Id: course_settings_updater.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_settings.component
 */
require_once dirname(__FILE__) . '/../course_settings_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../course/course_form.class.php';

class CourseSettingsToolUpdaterComponent extends CourseSettingsToolComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses settings');
        
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            $this->display_header($trail, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $form = new CourseForm(CourseForm :: TYPE_EDIT, $this->get_course(), $this->get_user(), $this->get_url(array(Tool :: PARAM_ACTION => CourseSettingsTool :: ACTION_UPDATE_COURSE_SETTINGS)));
        
        if ($form->validate())
        {
            $success = $form->update_course();
            $this->redirect(Translation :: get($success ? 'CourseSettingsUpdated' : 'CourseSettingsUpdateFailed'), ($success ? false : true), array(), array(WeblcmsManager :: PARAM_TOOL));
        }
        else
        {
            $this->display_header($trail, true);
            $form->display();
            $this->display_footer();
        }
    }
}
?>