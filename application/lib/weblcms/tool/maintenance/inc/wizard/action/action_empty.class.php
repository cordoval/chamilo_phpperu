<?php

require_once dirname(__FILE__) . '/../maintenance_wizard_process.class.php';

/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class ActionEmpty extends MaintenanceWizardProcess
{
	
	public static function factory($parent){
		$class = __CLASS__;
		return new $class($parent);
	}

	function perform($page, $actionName)
	{
		$values = $page->controller->exportValues();
		$publication_ids = array_keys($values['publications']);
		$dm = WeblcmsDataManager :: get_instance();
		$succes = true;

		foreach ($publication_ids as $id)
		{
			$publication = $dm->retrieve_content_object_publication($id);
			if (! $dm->delete_content_object_publication($publication))
			{
				$succes = false;
			}
		}

		$course_section_ids = array_keys($values['course_sections']);
		$condition = new InCondition(CourseSection :: PROPERTY_ID, $course_section_ids);
		$course_sections = $dm->retrieve_course_sections($condition);
		while ($course_section = $course_sections->next_result())
		{
			if (! $course_section->delete())
			{
				$succes = false;
			}
		}

		if ($values['content_object_categories'] == 1)
		{
			$condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
			$categories = $dm->retrieve_content_object_publication_categories($condition);
			while ($category = $categories->next_result())
			{
				if (! $category->get_allow_change()){
					continue;
				}

				if (! $category->delete())
				{
					$succes = false;
				}
			}
		}

		if ($succes)
		{
			$_SESSION['maintenance_message'] = Translation :: get('AllSelectedObjectsRemoved');
		}
		else
		{
			$_SESSION['maintenance_error_message'] = Translation :: get('NotAllSelectedObjectsRemoved');
		}
		$page->controller->container(true);
		$page->controller->run();
	}

}











?>