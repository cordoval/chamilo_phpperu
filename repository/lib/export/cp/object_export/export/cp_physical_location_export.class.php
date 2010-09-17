<?php

include_once Path::get_repository_path() .'/lib/content_object/physical_location/physical_location_display.class.php';

/**
 * Export PhysicalLocation objects. 
 * 
 * @copyright (c) 2010 University of Geneva 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpPhysicalLocationExport extends CpObjectExport{

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
		return $object instanceof PhysicalLocation || $object->get_type() == PhysicalLocation::get_type_name();
	}

	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();
		$content = $this->format($object);
		//$href = str_safe($object->get_title()).'.location.html'; 
		$href = $this->get_file_name($object, 'location.html');
		$directory = $settings->get_directory();
		$path = $directory.$href;
		if(Filesystem::write_to_file($path, $content, false)){
			$this->add_manifest_entry($object, $href);
			return $path;
		}else{
			return false;
		}
	}
	
	public function format(PhysicalLocation $object){
		return $this->get_description($object);
	}

    function get_description($object){
		$css = $this->get_main_css();
		$title = $object->get_title();
		$description = $object->get_description();
		$location = $object->get_location();
		
        $html = array();
		$html[] = '<html><head>';
		$html[] = "$css<title>$title</title>";
		$html[] = '<meta name="location" content="'.$object->get_location().'">';
		$html[] = '</head><body>';
        $html[] = '<div class="title">'.$title.'</div>';
        $html[] = '<div class="description">'.$description.'</div>';
        $html[] = '<div class="location">'.$object->get_location().'</div>';
        $html[] = '<div>';
        $html[] = $this->get_javascript($object);
        $html[] = '</div>';
        $html[] = '</body></html>';

        return implode("\n", $html);
    }

    function get_javascript($object)
    {
        $html = array();

        $html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/google_maps.js');
        $html[] = '<div id="map_canvas" style="width:100%; border: 1px solid black; height:500px"></div>';
        $html[] = '<script type="text/javascript">';
        $html[] = 'initialize(12);';
        $html[] = 'codeAddress(\'' . $object->get_location() . '\', \'' . $object->get_title() . '\');';
        $html[] = '</script>';

        return implode("\n", $html);
    }
}


?>