<?php

namespace application\weblcms;

use common\libraries\Utilities;
use common\libraries\Webapplication;

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
            'content_object_publication_user' => 'content_object_publication_user.class.php',
            'content_object_publication_group' => 'content_object_publication_group.class.php',
            'content_object_publication_course_group' => 'content_object_publication_course_group.class.php',
            'content_object_publication_category_manager' => 'category_manager/content_object_publication_category_manager.class.php',
            'object_publication_table_cell_renderer.class' => 'browser/object_publication_table/object_publication_table_cell_renderer.class.php',
            'course_type_settings' => 'course_type\course_type_settings.class.php',
            'course_type_layout' => 'course_type\course_type_layout.class.php',
            'course_type_rights' => 'course_type\course_type_rights.class.php',
            'course_settings' => 'course\course_settings.class.php',
            'course_rights' => 'course\course_rights.class.php'
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