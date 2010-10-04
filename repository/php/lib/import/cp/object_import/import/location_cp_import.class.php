<?php

/**
 * Import location files as PhysicalLocation objects.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class LocationCpImport extends CpObjectImportBase{

	public function get_extentions(){
		return array('location.html');
	}

	public function accept($settings){
		$path = $settings->get_path();
		$name = basename($path);
		$result = strpos($name, reset($this->get_extentions())) !== false;
		return $result;
	}

	protected function process_import(ObjectImportSettings $settings){
		$result = new PhysicalLocation();
		$result->set_location($this->get_location($settings));
		$result->set_description($this->get_description($settings));
		$this->save($settings, $result);
		return $result;
	}

	protected function get_location(ObjectImportSettings $settings){
		return $this->get_meta($settings, 'location');
	}
	
	protected function get_description(ObjectImportSettings $settings, $default = ''){
		if($doc = $settings->get_dom()){
			$list = $doc->getElementsByTagName('div');
			foreach($list as $div){
				if(strtolower($div->getAttribute('class')) == 'description'){
					$result = $this->get_innerhtml($div);
					return $result;
				}
			}
		}
		return $default;
	}
}






?>