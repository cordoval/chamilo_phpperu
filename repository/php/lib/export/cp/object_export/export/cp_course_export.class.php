<?php

/**
 * Export course publications. 
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpCourseExport extends CpObjectExport{

	public static function factory($settings){
		$object = $settings->get_object();
		if(self::accept($object)){
			return new self($settings);
		}else{
			return NULL;
		}
	}
	
	public static function accept($object){
		return $object instanceof Course;
	}

	public function get_type(){
		$result = ImscpObjectWriter::get_format_full_name() . '#Course';
		return $result;
	} 
	
	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();
		$children = chamilo::get_course_publications($object->get_id());
		foreach($children as $child){
			$child_object = $child->get_content_object();
			$href = $this->export_child($child_object);
		}
	}

}


















?>