<?php

require_once dirname(__FILE__) . '/../maintenance_wizard_process.class.php';

/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class ActionDelete extends MaintenanceWizardProcess
{
	
	public static function factory($parent){
		$class = __CLASS__;
		return new $class($parent);
	}

	function perform($page, $actionName)
	{
		$values = $page->controller->exportValues();
		$dm = WeblcmsDatamanager :: get_instance();
		$dm->delete_course($this->get_parent()->get_course_id());
		header('Location: ' . $this->get_parent()->get_path(WEB_PATH) . 'run.php?application=weblcms');
		exit();
		//$page->controller->container(true);
		//$page->controller->run();
	}

}
?>