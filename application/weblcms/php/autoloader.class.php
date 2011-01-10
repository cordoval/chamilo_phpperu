<?php

namespace application\weblcms;

use common\libraries\Utilities;
use common\libraries\WebApplication;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array(
            'weblcms_data_manager' => 'weblcms_data_manager.class.php',
            'content_object_publication' => 'content_object_publication.class.php',
            'content_object_publication_user' => 'content_object_publication_user.class.php',
            'content_object_publication_group' => 'content_object_publication_group.class.php',
            'weblcms_data_manager_interface' => 'weblcms_data_manager_interface.class.php',
            'content_object_publication_form' => 'content_object_publication_form.class.php',
            'weblcms_manager' => 'weblcms_manager/weblcms_manager.class.php',
            'course_layout' => 'course/course_layout.class.php',
            'course_form' => 'course/course_form.class.php',
            'course_group' => 'course_group/course_group.class.php',
            'course_group_form' => 'course_group/course_group_form.class.php',
            'course_group_subscriptions_form' => 'course_group/course_group_subscriptions_form.class.php',
            'common_request' => 'course/common_request.class.php',
            'tool' => 'tool/tool.class.php',
            'tool_component' => 'tool/tool_component.class.php',
            'course_module_last_access' => 'course/course_module_last_access.class.php',
            'content_object_publication_user' => 'content_object_publication_user.class.php',
            'content_object_publication_group' => 'content_object_publication_group.class.php',
            'content_object_publication_course_group' => 'content_object_publication_course_group.class.php',
            'content_object_publication_category' => 'category_manager/content_object_publication_category.class.php',
            'content_object_publication_category_manager' => 'category_manager/content_object_publication_category_manager.class.php',
            'object_publication_table_cell_renderer.class' => 'browser/object_publication_table/object_publication_table_cell_renderer.class.php',
            'course_type_settings' => 'course_type/course_type_settings.class.php',
            'course_type_layout' => 'course_type/course_type_layout.class.php',
            'course_type_rights' => 'course_type/course_type_rights.class.php',
            'course_type_tool' => 'course_type/course_type_tool.class.php',
            'course_type' => 'course_type/course_type.class.php',
            'course_settings' => 'course/course_settings.class.php',
            'course_rights' => 'course/course_rights.class.php',
            'weblcms_lp_attempt_tracker' => '../trackers/weblcms_lp_attempt_tracker.class.php',
            'weblcms_assessment_attempts_tracker' => '../trackers/weblcms_assessment_attempts_tracker.class.php',
            'course_category' => 'category_manager/course_category.class.php',
            'course' => 'course/course.class.php',
            'course_group_relation' => 'course/course_group_relation.class.php',
            'courses_rights_editor_manager' => 'courses_rights_editor/courses_rights_editor_manager.class.php',
            'database_weblcms_data_manager' => 'data_manager/database_weblcms_data_manager.class.php',
            'course_user_relation' => 'course/course_user_relation.class.php',
            'course_validator' => 'validator/course_validator.class.php',
            'default_course_category_table_cell_renderer' => 'course/course_category_table/default_course_category_table_cell_renderer.class.php',
            'mini_month_calendar_content_object_publication_list_renderer' => 'browser/list_renderer/mini_month_calendar_content_object_publication_list_renderer.class.php',
            'month_calendar_content_object_publication_list_renderer' => 'browser/list_renderer/month_calendar_content_object_publication_list_renderer.class.php',
            'week_calendar_content_object_publication_list_renderer' => 'browser/list_renderer/week_calendar_content_object_publication_list_renderer.class.php',
            'day_calendar_content_object_publication_list_renderer' => 'browser/list_renderer/day_calendar_content_object_publication_list_renderer.class.php',
            'content_object_publication_details_renderer' => 'browser/list_renderer/content_object_publication_details_renderer.class.php',
            'course_section' => 'course/course_section.class.php',
            'content_object_publication_browser' => 'content_object_publication_browser.class.php',
            'list_content_object_publication_list_renderer' => 'browser/list_renderer/list_content_object_publication_list_renderer.class.php',
            'content_object_publication_category_tree' => 'browser/content_object_publication_category_tree.class.php',
            'object_publication_table' => 'browser/object_publication_table/object_publication_table.class.php',
            'object_publication_table_cell_renderer' => 'browser/object_publication_table/object_publication_table_cell_renderer.class.php',
            'object_publication_table_column_model' => 'browser/object_publication_table/object_publication_table_column_model.class.php',
            'weblcms_learning_path_question_attempts_tracker' => '../trackers/weblcms_learning_path_question_attempts_tracker.class.php',
            'weblcms_lpi_attempt_objective_tracker' => '../trackers/weblcms_lpi_attempt_objective_tracker.class.php',
            'weblcms_lpi_attempt_tracker' => '../trackers/weblcms_lpi_attempt_tracker.class.php',
            'weblcms_lp_attempt_tracker' => '../trackers/weblcms_lp_attempt_tracker.class.php',
            'weblcms_survey_participant_mail_tracker' => '../trackers/weblcms_survey_participant_mail_tracker.class.php',
            'weblcms_survey_participant_tracker' => '../trackers/weblcms_survey_participant_tracker.class.php',
            'weblcms_survey_question_answer_tracker' => '../trackers/weblcms_survey_question_answer_tracker.class.php',
            'weblcms_lpi_attempt_interaction_tracker' => '../trackers/weblcms_lpi_attempt_interaction_tracker.class.php',
            'weblcms_rights' => 'weblcms_rights.class.php',
            'course_group_unsubscribe_right' => 'course/course_group_unsubscribe_right.class.php',
            'course_group_subscribe_right' => 'course/course_group_subscribe_right.class.php',
            'course_group_subscribe_right' => 'course/course_group_subscribe_right.class.php',
            'course_group_subscribe_right' => 'course/course_group_subscribe_right.class.php',
            'content_object_pub_feedback_browser' => 'content_object_pub_feedback_browser.class.php',
            'list_content_object_publication_list_renderer' => 'browser/list_renderer/list_content_object_publication_list_renderer.class.php',
            'weblcms_block' => '../blocks/weblcms_block.class.php',
            'course_list_renderer' => 'course/course_list_renderer/course_list_renderer.class.php',
            'course_user_category' => 'course/course_user_category.class.php',
        );

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('weblcms') . $url;
            return true;
        }

        return false;
    }

}

?>
