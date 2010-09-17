<?php

require_once dirname(__FILE__) . '/../maintenance_wizard_process.class.php';
require_once Path::get_repository_path() .'lib/export/content_object_export.class.php';
require_once Path::get_repository_path() . '/lib/export/cp/cp_export.class.php';

/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class ActionExportCp extends MaintenanceWizardProcess
{

	public static function factory($parent){
		$class = __CLASS__;
		return new $class($parent);
	}

	function perform($page, $actionName){
		$dm = WeblcmsDataManager :: get_instance();
		//$course_id = $this->get_parent()->get_course_id();
		//$course = WeblcmsDataManager::get_instance()->retrieve_course($course_id);
		$values = $page->controller->exportValues();
		$publication_ids = array_keys($values['publications']);
		$objects = array();
		foreach ($publication_ids as $id){
			$publication = $dm->retrieve_content_object_publication($id);
			$objects[] = $publication->get_content_object();
		}
		$exporter = ContentObjectExport::factory('cp', $objects);
		$path = $exporter->export_content_object();

		//$exporter = ContentObjectExport::factory('cp', $course);
		//$path = $exporter->export_content_object();

		Filesystem :: copy_file($path, Path :: get(SYS_TEMP_PATH) . Session::get_user_id() . '/course.zip', true);
		$webpath = Path :: get(WEB_TEMP_PATH) . Session::get_user_id() . '/course.zip';

		$_SESSION['maintenance_message'] = '<a href="' . $webpath . '">' . Translation::get('Download') . '</a>';

		$page->controller->container(true);
		$page->controller->run();
	}

}


?>