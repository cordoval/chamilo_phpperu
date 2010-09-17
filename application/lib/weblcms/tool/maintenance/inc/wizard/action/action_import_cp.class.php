<?php

require_once dirname(__FILE__) . '/../maintenance_wizard_process.class.php';
require_once dirname(__FILE__) . '/../cp_import_selection_maintenance_wizard_page.class.php';
require_once Path::get_repository_path() .'lib/import/content_object_import.class.php';
require_once Path::get_repository_path() . '/lib/import/cp/cp_import.class.php';

/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class ActionImportCp extends MaintenanceWizardProcess
{

	public static function factory($parent){
		$class = __CLASS__;
		return new $class($parent);
	}

	function perform($page, $actionName){
		$values = $page->controller->exportValues();
		$category_id = $values[CpImportSelectionMaintenanceWizardPage::CATEGORY_ID];
		$file = $_FILES[CpImportSelectionMaintenanceWizardPage::IMPORT_FILE_NAME];
		$user = $this->get_user();

		$importer = ContentObjectImport::factory('cp', $file, $user, $category_id);
		$result = $importer->import_content_object();
		if($result){
			$course = $this->get_course();
			$importer->publish($course, $result);
		}
		
		$notice = array();

		$messages = $importer->get_messages();
		$warnings = $importer->get_warnings();
		$errors = $importer->get_errors();

		if($result){
			$messages[] = Translation::translate('ContentObjectImported');
		}else{
			$errors[] = Translation::translate('ContentObjectNotImported');
		}

		if(count($messages)>0){
			$notice = implode('<br/>', $messages);
			Session::register('maintenance_message', $notice);
		}
		if(count($warnings)>0){
			$notice = implode('<br/>', $warnings);
			Session::register('maintenance_warning_message', $notice);
		}
		if(count($errors)>0){
			$notice = implode('<br/>', $errors);
			Session::register('maintenance_error_message', $notice);
		}

		$page->controller->container(true);
		$page->controller->run();

		return $result;
	}

	public function get_user(){
		$result = UserDataManager::get_instance()->retrieve_user(Session::get_user_id());
		return $result;
	}

	public function get_course(){
		$dm = WeblcmsDataManager :: get_instance();
		$course_id = $this->get_parent()->get_course_id();
		return $dm->retrieve_course($course_id);
	}

}


?>