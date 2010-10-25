<?php

require_once dirname(__FILE__) . '/../maintenance_wizard_process.class.php';

/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class ActionBackup extends MaintenanceWizardProcess
{
	
	public static function factory($parent){
		$class = __CLASS__;
		return new $class($parent);
	}

	function perform($page, $actionName)
	{
		$values = $page->controller->exportValues();
		$_SESSION['maintenance_error_message'] = 'BACKUP: TODO';
		$page->controller->container(true);
		$page->controller->run();
	}

}










?>