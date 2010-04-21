<?php

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

dump("Course settings writable test:\n");

$course = null;
for($i = 0; $i < 2; $i++)
{
	$course = WeblcmsDataManager::get_instance()->retrieve_empty_course();
	switch($i)
	{
		case 0: $course->set_course_type(NULL);
				dump("Created new course without coursetype\n");
				break;
		case 1: $course->set_course_type(new CourseType());
				dump("Created new course with empty coursetype\n");
				break;
	}
	dump("Trying to write to settings\n");
	$course->set_language("Dutch");
	$course->set_visibility(1);
	$course->set_access(1);
	$course->set_max_number_of_members(30);
	dump("Write complete\n");
	dump($course->get_settings()->get_default_properties());
	dump("Trying to write to layout settings\n");
	$course->set_feedback(1);
	$course->set_layout(1);
	$course->set_tool_shortcut(1);
	$course->set_menu(1);
	$course->set_breadcrumb(1);
	$course->set_intro_text(1);
	$course->set_student_view(1);
	$course->set_course_code_visible(1);
	$course->set_course_manager_name_visible(1);
	$course->set_course_languages_visible(1);
	dump("Write complete\n");
	dump($course->get_layout_settings()->get_default_properties());
	dump("Trying to write to rights\n");
	$course->set_direct_subscribe_available(1);
	$course->set_request_subscribe_available(1);
	$course->set_code_subscribe_available(1);
	$course->set_code("Mellow");
	$course->set_unsubscribe_available(1);
	dump("Write complete\n");
	dump($course->get_rights()->get_default_properties());
}
?>

