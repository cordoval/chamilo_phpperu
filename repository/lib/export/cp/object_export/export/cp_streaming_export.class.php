<?php

/**
 * Export streaming video clip objects.
 *
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpStreamingExport extends CpObjectExport{

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
		return 	($object instanceof Youtube || $object->get_type() == Youtube::get_type_name()) ||
				($object instanceof Dailymotion || $object->get_type() == Dailymotion::get_type_name()) ||
				($object instanceof Vimeo || $object->get_type() == Vimeo::get_type_name());
	}

	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();
		$content = $this->format($object);
		//$href = str_safe($object->get_title()).'.video.url';
		$href = $this->get_file_name($object, 'video.url');
		$directory = $settings->get_directory();
		$path = $directory.$href;
		if(Filesystem::write_to_file($path, $content, false)){
			$this->add_manifest_entry($object, $href);
			return $path;
		}else{
			return false;
		}
	}

	public function format($object){
		$result = "[InternetShortcut]\n";
		$result .="URL={$object->get_url()}";
		return $result;
	}

}


?>