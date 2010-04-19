<?php

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

$registered_group_id = array();

dump("Checking subscribe:");
dump("Installing groups to test with");
dump("Installing parent");

$group = new Group();
$group->set_name("Parent Group");
$group->set_parent(1);
$group->set_code("ParentGroup");
$group->create();
$parent_group = $group;
$registered_group_id[$group->get_id()] = CourseGroupSubscribeRight :: SUBSCRIBE_NONE;

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
		case 1: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_DIRECT;
				$rights = new CourseGroupSubscribeRight();
				$rights->set_course_id(1000);
				$rights->set_group_id($group->get_id());
				$rights->set_subscribe(CourseGroupSubscribeRight::SUBSCRIBE_DIRECT);
				$rights->create();
				break;
		case 2: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_NONE;
				break;
		case 3: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_REQUEST;
				$rights = new CourseGroupSubscribeRight();
				$rights->set_course_id(1000);
				$rights->set_group_id($group->get_id());
				$rights->set_subscribe(CourseGroupSubscribeRight::SUBSCRIBE_REQUEST);
				$rights->create();
				break;
		case 4: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_NONE;
				break;
		case 5: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_CODE;
				$rights = new CourseGroupSubscribeRight();
				$rights->set_course_id(1000);
				$rights->set_group_id($group->get_id());
				$rights->set_subscribe(CourseGroupSubscribeRight::SUBSCRIBE_CODE);
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
			$registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_NONE;
			
			if($i == 1)
				$registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_DIRECT;
			else
			{
				switch($j)
				{
					case 1: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_REQUEST;
							$rights = new CourseGroupSubscribeRight();
							$rights->set_course_id(1000);
							$rights->set_group_id($group->get_id());
							$rights->set_subscribe(CourseGroupSubscribeRight::SUBSCRIBE_REQUEST);
							$rights->create();
							break;
					case 2: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_REQUEST;
							$rights = new CourseGroupSubscribeRight();
							$rights->set_course_id(1000);
							$rights->set_group_id($group->get_id());
							$rights->set_subscribe(CourseGroupSubscribeRight::SUBSCRIBE_REQUEST);
							$rights->create();
							break;
					case 3: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_DIRECT;
							$rights = new CourseGroupSubscribeRight();
							$rights->set_course_id(1000);
							$rights->set_group_id($group->get_id());
							$rights->set_subscribe(CourseGroupSubscribeRight::SUBSCRIBE_DIRECT);
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
					$registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_DIRECT;
				else
					$registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_REQUEST;
				
			}
		}
	}
}
dump("registered groups:");
dump($registered_group_id);
dump("checking registered groups with rights from can_group_subscribe function");
$course_rights = new CourseRights();
$course_rights->set_course_id(1000);
$course_rights->set_direct_subscribe_available(1);
$course_rights->set_request_subscribe_available(1);
$course_rights->set_code_subscribe_available(1);
foreach($registered_group_id as $index => $value)
{
	if($value == $course_rights->can_group_subscribe($index))
		dump("True " . $value . " == " . $course_rights->can_group_subscribe($index));
	else
		dump("False " . $value . " != " . $course_rights->can_group_subscribe($index));
}
dump("Deleting records");
GroupDataManager::get_instance()->delete_group($parent_group);
$condition = new EqualityCondition(CourseGroupSubscribeRight :: PROPERTY_COURSE_ID, 1000);
$db = new Database();
$db->delete_objects('weblcms_' . CourseGroupSubscribeRight :: get_table_name(), $condition);

dump("Checkin unsubscribe");
dump("Installing groups to test with");
dump("Installing parent");
$registered_group_id = array();

$group = new Group();
$group->set_name("Parent Group");
$group->set_parent(1);
$group->set_code("ParentGroup");
$group->create();
$parent_group = $group;
$registered_group_id[$group->get_id()] = CourseGroupSubscribeRight :: SUBSCRIBE_NONE;

dump("Installing children");

for($i=1;$i<4;$i++)
{
	$group = new Group();
	$group->set_name("Child Group " + $i);
	$group->set_parent($parent_group->get_id());
	$group->set_code("ChildGroup" + $i);
	$group->create();
	$child_id = $group->get_id();
	switch($i)
	{
		case 1: $registered_group_id[$group->get_id()] = 1;
				$rights = new CourseGroupUnsubscribeRight();
				$rights->set_course_id(1000);
				$rights->set_group_id($group->get_id());
				$rights->set_unsubscribe(1);
				$rights->create();
				break;
		case 2: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_NONE;
				break;
		case 3: $registered_group_id[$group->get_id()] = 1;
				$rights = new CourseGroupUnsubscribeRight();
				$rights->set_course_id(1000);
				$rights->set_group_id($group->get_id());
				$rights->set_unsubscribe(1);
				$rights->create();
				break;
	}
	
	if($i==2)
	{
		for($j=1;$j<3;$j++)
		{
			$group = new Group();
			$group->set_name("Child child Group " + $i + " " + $j);
			$group->set_parent($child_id);
			$group->set_code("ChildChildGroup" + $i + "" + $j);
			$group->create();
			$child_child_id = $group->get_id();
			
			switch($j)
			{
				case 1: $registered_group_id[$group->get_id()] = CourseGroupSubscribeRight::SUBSCRIBE_NONE;
						break;
				case 2: $registered_group_id[$group->get_id()] = 1;
						$rights = new CourseGroupUnsubscribeRight();
						$rights->set_course_id(1000);
						$rights->set_group_id($group->get_id());
						$rights->set_unsubscribe(1);
						$rights->create();
						break;
			}
	
			if($j == 2)
			{
				$group = new Group();
				$group->set_name("Child child child Group " + $i + " " + $j + " " + 1);
				$group->set_parent($child_child_id);
				$group->set_code("ChildChildGroup" + $i + "" + $j + "" + 1);
				$group->create();
				$registered_group_id[$group->get_id()] = 1;
			}
		}
	}
}
dump("registered groups:");
dump($registered_group_id);
dump("checking registered groups with rights from can_group_subscribe function");
$course_rights = new CourseRights();
$course_rights->set_course_id(1000);
$course_rights->set_unsubscribe_available(1);
foreach($registered_group_id as $index => $value)
{
	if($value == $course_rights->can_group_unsubscribe($index))
		dump("True " . $value . " == " . $course_rights->can_group_unsubscribe($index));
	else
		dump("False " . $value . " != " . $course_rights->can_group_unsubscribe($index));
}
dump("Deleting records");
GroupDataManager::get_instance()->delete_group($parent_group);
$condition = new EqualityCondition(CourseGroupUnsubscribeRight :: PROPERTY_COURSE_ID, 1000);
$db = new Database();
$db->delete_objects('weblcms_' . CourseGroupUnsubscribeRight :: get_table_name(), $condition);
?>