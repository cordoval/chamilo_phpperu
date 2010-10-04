<?php

/**
 * Imports a folder resource as a Document object. Zip the folder in order to attach it to the document.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 */
class FolderCpImport extends ImscpManifestCpImport{

	public function get_weight(){
		return 1000000;
	}

	public function get_extentions(){
		return array();
	}

	public function accept($settings){
		$path = $settings->get_path();
		return is_dir($path);
	}

	protected function process_import(ObjectImportSettings $settings){
		$path = $settings->get_path();

		//for whatever reason unzipping replaces __ , ___ , ____ , ... with _
		//@todo: fix unzipping instead of compensating here
		if(!file_exists($path)){
			$path = str_replace('__', '_', $path); //handles __
			$path = str_replace('__', '_', $path); //handles ___
			$path = str_replace('__', '_', $path); //...
			$path = str_replace('__', '_', $path);
		}


		$zip = Filecompression::factory();
		$zipped_file = $zip->create_archive($path);

		$filename = $settings->get_filename();

		$result = new Document();
		$result->set_description($filename);
		$result->set_filename("$filename.zip");
		$result->set_temporary_file_path($zipped_file);
		$result->set_filesize(Filesystem::get_disk_space($path));
		$result->set_hash(md5($filename));
		$this->save($settings, $result);

		Filesystem::remove($zipped_file);

		return $result;
	}

}







