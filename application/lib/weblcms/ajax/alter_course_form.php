<?php
/**
 * $Id: block_sort.php 227 2009-11-13 14:45:05Z kariboe $
 * @package application.weblcms.ajax
 */
$this_section = 'weblcms';

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course_form.class.php';

$course_id = $_POST['course_id'];
$course_type_id = $_POST['course_type_id'];

$wdm = WeblcmsDataManager :: get_instance();
$course_types = $wdm->retrieve_active_course_types();
$validation = false;

while($course_type = $course_types->next_result())
{
	if($course_type->get_id() == $course_type_id)
		$validation = true;
}

if (Authentication :: is_valid() && $validation)
{
	$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
	if($course_id != '')
		$course = $wdm->retrieve_course($course_id);
	else
		$course = $wdm->retrieve_empty_course();
	
	$course_type = $wdm->retrieve_course_type($course_type_id);
	$course->set_course_type($course_type);
   
    $id = $course->get_id();
	if(empty($id))
	{
		$url = Redirect :: get_link($this_section, array('go'=>WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_CREATOR));
		$form = new CourseForm(CourseForm :: TYPE_CREATE, $course, $user, $url, null);
	}
	else
	{
		$url = Redirect :: get_link($this_section, array('go'=>WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_CREATOR,'course'=>$id));
		$form = new CourseForm(CourseForm :: TYPE_EDIT, $course, $user, $url, null);
	}
	$form->display();
}

echo($course_id . " " . $course_type_id);
?>