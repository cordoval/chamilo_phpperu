<?php

/**
 * Qti Export. Delegate works to the QTI module.
 *
 *
 * @copyright (c) 2010 University of Geneva
 *
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpQtiExport extends CpObjectExport{

	public static function factory($settings){
		$object = $settings->get_object();
		if(self::accept($object)){
			return new self($settings);
		}else{
			return NULL;
		}
	}

	public static function accept($object){
		if(! $object instanceof ContentObject){
			return false;
		}
		return strpos(strtolower($object->get_type()), 'question') ||
		$object instanceof Assessment ||
		$object instanceof Survey;
	}

	public function export_content_object(){
		if($result = $this->export_test()){
			return $result;
		}
		if($result = $this->export_question()){
			return $result;
		}
	}

	protected function is_test(){
		$settings = $this->get_settings();
		$object = $settings->get_object();
		return $object instanceof Assessment || $object instanceof Survey;
	}

	protected function is_question(){
		$settings = $this->get_settings();
		$object = $settings->get_object();
		return strpos(strtolower($object->get_type()), 'question') ;
	}

	protected function export_test(){
		if(! $this->is_test()){
			return false;
		}

		$settings = $this->get_settings();
		$object = $settings->get_object();
		$directory = $settings->get_directory();
		$manifest = $settings->get_manifest();
		$toc = $settings->get_toc();
		$export = ContentObjectExport::factory('qti', $object);
		if($path = $export->export_content_object()){
			$directory = $settings->get_directory();
			$href = $this->get_file_name($object, 'qti.zip');
			Filesystem::copy_file($path, $directory.$href, true);
			Filesystem::remove($path);
			$this->add_manifest_entry($object, $href);
		}
		 
		return $path;
	}

	protected function export_question(){
		if(! $this->is_question()){
			return false;
		}
		$settings = $this->get_settings();
		$object = $settings->get_object();
		$directory = $settings->get_directory();
		$manifest = $settings->get_manifest();
		$toc = $settings->get_toc();
		$export = QtiExport::factory_qti($object, $directory, $manifest, $toc);
		$path = $export->export_content_object();
		return $path;
	}

}


