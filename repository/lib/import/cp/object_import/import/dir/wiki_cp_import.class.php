<?php

include_once dirname(__FILE__) . '/imscp_manifest_cp_import.class.php';

/**
 * Imports a wiki IMSCP directory as a wiki object.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class WikiCpImport extends ImscpManifestCpImport{

	protected $children = array();

	public function get_weight(){
		return 1;
	}

	public function get_extentions(){
		return array('wiki');
	}

	public function accept(ObjectImportSettings $settings){
		$directory = $settings->get_path();
		if(strpos($directory, reset($this->get_extentions())) == false){
			return false;
		}
		$manifest = $settings->get_manifest_reader();
		$name = $manifest->get_root()->name();
		$location = $manifest->get_root()->get_attribute('xsi:schemaLocation');
		return $name == 'manifest' && strpos($location, 'http://www.imsglobal.org') !== false;
	}

	protected function process_import($settings){
		$this->children = array();
		$this->import_manifest($settings);
		$result = new Wiki();
		$result->set_locked(false);
		$result->set_links('');
		
    	$store = ContentObject::get_data_manager();
		$this->save($settings, $result);
		foreach($this->children as $child){
			$cloi = ComplexContentObjectItem::factory($child->get_type());
			$cloi->set_ref($child->get_id());
			$cloi->set_user_id($settings->get_user()->get_id());
			$cloi->set_parent($result->get_id());
			$cloi->set_display_order($store->select_next_display_order($result->get_id()));
			$cloi->save();
		}
		$this->children = array();

		return $result;
	}

	protected function import_manifest($settings){
		$manifest = $settings->get_manifest_reader()->get_root();
		if($result = $this->import_organizations($settings, $manifest->all_organization())){
			return $result;
		}else{
			return $this->import_resources($settings, $manifest->get_resources()->list_resource());
		}
	}

	protected function import_child(ObjectImportSettings $settings){
		if($result = $this->get_root()->import($settings)){
			$this->children[] = $result;
		}
		return $result;
	}
}



