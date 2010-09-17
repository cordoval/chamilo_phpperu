<?php

/**
 * Perform a -->Directory<-- import by looking up for a manifest file and importing it.
 *
 * Look for an IMSCP manifest in the directory pointed to.
 * Read the IMSCP manifest and imports its organization as individual object for the first level and as a package for sublevels.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class ImscpManifestCpImport extends CpObjectImportBase{

	public function get_weight(){
		return 100;
	}

	public function get_extentions(){
		return array();
	}

	public function accept(ObjectImportSettings $settings){
		/*if($settings->get_level()>1){
			return false; //i.e. import the first level only import others as imscp
		}*/
		$manifest = $settings->get_manifest_reader();
		$name = $manifest->get_root()->name();
		$location = $manifest->get_root()->get_attribute('xsi:schemaLocation');
		return $name == 'manifest' && strpos($location, 'http://www.imsglobal.org') !== false;
	}

	protected function process_import($settings){
		$manifest = $settings->get_manifest_reader()->get_root();
		if($result = $this->import_organizations($settings, $manifest->all_organization())){
			return $result;
		}else{
			return $this->import_resources($settings, $manifest->get_resources()->list_resource());
		}
	}

	protected function import_organizations($settings, $organizations){
		if(empty($organizations)){
			return false;
		}
		$organization = reset($organizations);
		return $this->import_organization($settings, $organization);
	}

	protected function import_organization($settings, ImscpManifestReader $org){
		$result = array();
		$items = $org->list_item();
		foreach($items as $item){
			if($import = $this->import_item($settings, $item)){
				$result = array_merge($result, is_array($import) ? $import : array($import));
			}
		}
		return $result;
	}

	protected function import_item($settings, ImscpManifestReader $item){
		$result = array();
		$title = $item->get_title()->value();
		$resource = $item->navigate();
		if($import = $this->import_resource($settings, $resource, $title)){
			$result = array_merge($result, is_array($import) ? $import : array($import));
		}
		$children = $item->list_item();
		foreach($children as $child){
			if($import = $this->import_item($settings, $child)){
				$result = array_merge($result, is_array($import) ? $import : array($import));
			}
		}
		return $result;
	}

	protected function import_resources($settings, $resources){
		$result = array();
		foreach($resources as $resource){
			if($import = $this->import_resource($settings, $resource)){
				$result = array_merge($result, is_array($import) ? $import : array($import));
			}
		}
		return $result;
	}

	protected function import_resource(ObjectImportSettings $settings, ImscpManifestReader $resource, $title = ''){
		$href = $resource->href;
		if(empty($href)){
			$files = $resource->list_file();
			$file = empty($files) ? false : reset($files);
			if($file){
				$href = $file->href;
			}
		}
		$type = $resource->type;
		if(!empty($href)){
			$title = empty($title) ? basename($href) : $title;
			$dir = $settings->get_path();
			$ext = end(explode('.', $href));
			$item_settings = $settings->copy("$dir/$href", $title, $ext, $type);
			return $this->import_child($item_settings);
		}else{
			return false;
		}
	}

	protected function import_child(ObjectImportSettings $settings){
		return $this->get_root()->import($settings);
	}

}







