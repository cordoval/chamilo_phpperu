<?php

/**
 * Export announcement objects.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpAnnouncementExport extends CpObjectExport{

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
		return $object instanceof Announcement || $object->get_type() == Announcement::get_type_name();
	}

	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();
		$content = $this->format($object);
    	$href = $this->get_file_name($object, 'announcement.html');
		$directory = $settings->get_directory();
		$path = $directory.$href;
		if(Filesystem::write_to_file($path, $content, false)){
			$this->add_manifest_entry($object, $href);
			return $path;
		}else{
			return false;
		}
	}
	
	protected function format(Announcement $object){
		$css = $this->get_main_css();
		$title = $object->get_title();
		$description = $object->get_description();
		$result = "<html><head>$css<title>$title</title></head><body>";
		$result .= '<div class="title">'.$title.'</div>';
		$result .= '<div class="description">'. $description . '</div>';
		$result .= '</body></html>';
		return $result;
	}

}


?>