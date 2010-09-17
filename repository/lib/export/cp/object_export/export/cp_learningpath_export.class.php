<?php

/**
 * Scorm/LearningPath Export. Delegates work to the Scorm module.
 *
 *
 * @copyright (c) 2010 University of Geneva
 *
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpLearningpathExport extends CpObjectExport{

	public static function factory($settings){
		$object = $settings->get_object();
		if(self::accept($object)){
			return new self($settings);
		}else{
			return NULL;
		}
	}

	/**
	 * @todo: Enable class when SCORM module is fixed. 
	 * 
	 * @param ContentObject|Course $object
	 * @return boolean
	 */
	public static function accept($object){
		
		return false; //@todo: !!!!! Remove that when SCORM module is fixed.
		
		if(! $object instanceof ContentObject){
			return false;
		}
		$result = $object instanceof LearningPath || $object->get_type() == LearningPath::get_type_name();
		return $result;
	}

	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();

		$export = ContentObjectExport::factory('scorm', $object);
		if($path = $export->export_content_object()){
    		$href = $this->get_file_name($object, 'scorm');
			$directory = $settings->get_directory();
			Filesystem::copy_file($path, $directory.$href, true);
			Filesystem::remove($path);
			$this->add_manifest_entry($object, $href);
		}
		 
		return $path;
	}

}















?>