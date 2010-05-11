<?php
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_item.class.php';

require_once dirname(__FILE__) . '/course/course_user_relation.class.php';

class WeblcmsGradebookTreeMenuDataProvider extends GradebookTreeMenuDataProvider
{
	public function get_tree_menu_data()
	{
		$menu_item = new TreeMenuItem();
		$menu_item->set_title(Translation :: get('Courses'));
		$menu_item->set_id(0);
		$menu_item->set_url($this->get_url());
		$menu_item->set_class('home');
		
        $condition = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, Session :: get_user_id(), CourseUserRelation :: get_table_name());
		$courses = WeblcmsDataManager :: get_instance()->retrieve_user_courses($condition);
		
		while($course = $courses->next_result())
		{
			$course_item = new TreeMenuItem();
			$course_item->set_title($course->get_name());
			$course_item->set_id($course->get_id());
			$course_item->set_class('course');
			$course_item->set_url($this->get_url());
			$course_item->set_collapsed(true);
			$tools = $course->get_tools();
			if($this->get_type() == 'internal')
			{
				foreach($tools as $tool)
				{
	        		if(PlatformSetting :: get_instance()->get('allow_evaluate_' . $tool->name, 'gradebook'))
	        		{
						$tool_item = new TreeMenuItem();
						$tool_item->set_title($tool->name);
						$tool_item->set_id();
						$tool_item->set_url($this->format_url('C' . $course->get_id() . '_T'.$tool->name));
						$tool_item->set_class('tool');
						$course_item->add_child($tool_item);
	        		}
				}
			}
			$menu_item->add_child($course_item);
		}
		return $menu_item;
	}
}
?>