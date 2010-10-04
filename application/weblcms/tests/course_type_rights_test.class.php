<?php

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

$registered_group_id = array();

dump("Checking create:");
dump("Installing groups to test with");
dump("Installing parent");

$group = new Group();
$group->set_name("Parent Group");
$group->set_parent(1);
$group->set_code("ParentGroup");
$group->create();
$parent_group = $group;
$registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight :: CREATE_NONE;

dump("Installing children");

for($i=1;$i<6;$i++)
{
	$group = new Group();
	$group->set_name("Child Group " + $i);
	$group->set_parent($parent_group->get_id());
	$group->set_code("ChildGroup" + $i);
	$group->create();
	$child_id = $group->get_id();
	switch($i)
	{
		case 1: $registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_DIRECT;
				$rights = new CourseTypeGroupCreationRight();
				$rights->set_course_type_id(1000);
				$rights->set_group_id($group->get_id());
				$rights->set_create(CourseTypeGroupCreationRight::CREATE_DIRECT);
				$rights->create();
				break;
		case 2: $registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_NONE;
				break;
		case 3: $registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_REQUEST;
				$rights = new CourseTypeGroupCreationRight();
				$rights->set_course_type_id(1000);
				$rights->set_group_id($group->get_id());
				$rights->set_create(CourseTypeGroupCreationRight::CREATE_REQUEST);
				$rights->create();
				break;
		case 4: $registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_NONE;
				break;
		case 5: $registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_DIRECT;
				$rights = new CourseTypeGroupCreationRight();
				$rights->set_course_type_id(1000);
				$rights->set_group_id($group->get_id());
				$rights->set_create(CourseTypeGroupCreationRight::CREATE_DIRECT);
				$rights->create();
				break;
	}
	
	if($i==1 || $i==2)
	{
		for($j=1;$j<4;$j++)
		{
			$group = new Group();
			$group->set_name("Child child Group " + $i + " " + $j);
			$group->set_parent($child_id);
			$group->set_code("ChildChildGroup" + $i + "" + $j);
			$group->create();
			$child_child_id = $group->get_id();
			$registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_NONE;
			
			if($i == 1)
				$registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_DIRECT;
			else
			{
				switch($j)
				{
					case 1: $registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_REQUEST;
							$rights = new CourseTypeGroupCreationRight();
							$rights->set_course_type_id(1000);
							$rights->set_group_id($group->get_id());
							$rights->set_create(CourseTypeGroupCreationRight::CREATE_REQUEST);
							$rights->create();
							break;
					case 2: $registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_REQUEST;
							$rights = new CourseTypeGroupCreationRight();
							$rights->set_course_type_id(1000);
							$rights->set_group_id($group->get_id());
							$rights->set_create(CourseTypeGroupCreationRight::CREATE_REQUEST);
							$rights->create();
							break;
					case 3: $registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_DIRECT;
							$rights = new CourseTypeGroupCreationRight();
							$rights->set_course_type_id(1000);
							$rights->set_group_id($group->get_id());
							$rights->set_create(CourseTypeGroupCreationRight::CREATE_DIRECT);
							$rights->create();
							break;
				}
			}
	
			if($j == 1)
			{
				$group = new Group();
				$group->set_name("Child child child Group " + $i + " " + $j + " " + 1);
				$group->set_parent($child_child_id);
				$group->set_code("ChildChildGroup" + $i + "" + $j + "" + 1);
				$group->create();
				if($i == 1)
					$registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_DIRECT;
				else
					$registered_group_id[$group->get_id()] = CourseTypeGroupCreationRight::CREATE_REQUEST;
				
			}
		}
	}
}
dump("registered groups:");
dump($registered_group_id);
dump("checking registered groups with rights from can_group_create function");
$course_type_rights = new CourseTypeRights();
$course_type_rights->set_course_type_id(1000);
$course_type_rights->set_creation_available(1);
$course_type_rights->set_creation_on_request_available(1);
foreach($registered_group_id as $index => $value)
{
	if($value == $course_type_rights->can_group_create($index))
		dump("True " . $value . " == " . $course_type_rights->can_group_create($index));
	else
		dump("False " . $value . " != " . $course_type_rights->can_group_create($index));
}
dump("Deleting records");
GroupDataManager::get_instance()->delete_group($parent_group);
$condition = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, 1000);
$db = new Database();
$db->delete_objects('weblcms_' . CourseTypeGroupCreationRight :: get_table_name(), $condition);
?>