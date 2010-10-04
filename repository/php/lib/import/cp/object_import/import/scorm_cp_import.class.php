<?php

require_once Path::get_repository_path() .'lib/import/qti/main.php';

/**
 * Import IMS SCORM files. Delegate the work to the SCORM import module.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class ScormCpImport extends CpObjectImportBase{

	/**
	 * @see repository/lib/import/cp/object_import/CpObjectImportBase::get_extentions()
	 */
	public function get_extentions(){
		return array('zip');
	}

	/**
	 * @see repository/lib/import/cp/object_import/CpObjectImportBase::accept()
	 */
	public function accept($settings){
		$manifest = $settings->get_manifest_reader();
		$name = $manifest->get_root()->name();
		$location = $manifest->get_root()->get_attribute('xsi:schemaLocation');
		$result = ($name == 'manifest') && (strpos($location, 'http://www.adlnet.org') !== false);
		return $result;
	}

	protected function process_import(ObjectImportSettings $settings){
		$file = array();

		$zip = Filecompression::factory();
		$filepath = $zip->create_archive($settings->get_path());

		$file['name'] = $settings->get_filename() . '.zip';
		$file['tmp_name'] = $filepath;
		$file['type'] = 'appplication/x-gzip';
		$file['error'] = 0;
		$file['size'] = filesize($filepath);

		$user = $settings->get_user();
		$category_id = $settings->get_category_id();

		$importer = ContentObjectImport::factory('scorm', $file, $user, $category_id);
		$result = $importer->import_content_object();
		Filesystem::remove($filepath);
		return $result;
	}



}






?>