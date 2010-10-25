<?php

require_once dirname(__FILE__) . '/../maintenance_wizard_process.class.php';

/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class ActionCopy extends MaintenanceWizardProcess
{
	
	public static function factory($parent){
		$class = __CLASS__;
		return new $class($parent);
	}

	function perform($page, $actionName)
	{
		$values = $page->controller->exportValues();


		$dm = WeblcmsDataManager :: get_instance();

		$course_section_ids = array_keys($values['course_sections']);
		$condition = new InCondition(CourseSection :: PROPERTY_ID, $course_section_ids);
		$course_sections = $dm->retrieve_course_sections($condition);
		while ($course_section = $course_sections->next_result())
		{
			$courses = $values['course'];
			foreach ($courses as $course_code)
			{
				$course_section->set_id(null);
				$course_section->set_course_code($course_code);
				$course_section->create();
			}
		}

		$category_ids = array();

		if ($values['content_object_categories'] == 1)
		{
			$condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
			$categories = $dm->retrieve_content_object_publication_categories($condition);
			while ($category = $categories->next_result())
			{
				if (! $category->get_allow_change()){
					continue;
				}

				$courses = $values['course'];
				$parent = $category->get_parent();
				$id = $category->get_id();

				foreach ($courses as $course_code)
				{

					$category->set_id(null);
					$category->set_course($course_code);

					if ($parent != 0)
					{
						$category->set_parent($category_ids[$parent]['course_code']);
					}

					$category->create();
					$category_ids[$id]['course_code'] = $category->get_id();
				}
			}
		}

		$publication_ids = array_keys($values['publications']);
		foreach ($publication_ids as $id)
		{
			$publication = $dm->retrieve_content_object_publication($id);
			$courses = $values['course'];
			$parent = $publication->get_category_id();

			foreach ($courses as $course_code)
			{
				$publication->set_id(null);
				$publication->set_course_id($course_code);

				if ($parent != 0)
				{
					$publication->set_category_id($category_ids[$parent]['course_code']);
				}

				$publication->create();
			}
		}

		$_SESSION['maintenance_message'] = Translation :: get('CopyFinished');

		$page->controller->container(true);
		$page->controller->run();
	}

}
?>