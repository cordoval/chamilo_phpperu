<?php

/**
 * Import a file as a Document object. Accept all kind of files. Called last.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class DocumentCpImport extends CpObjectImportBase{

	public function get_extentions(){
		return array('*');
	}

	public function accept($settings){
		return ! is_dir($settings->get_path());
	}

	public function get_weight(){
		return 1000000;
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
		 
		$filename = $settings->get_filename();
		$ext = $settings->get_extention();
		$result = new Document();
		$result->set_description($filename);
		$result->set_filename("$filename.$ext");
		$result->set_temporary_file_path($path);
		$result->set_filesize(Filesystem::get_disk_space($path));
		$result->set_hash(md5($filename));
		$this->save($settings, $result);
		return $result;
	}
}







