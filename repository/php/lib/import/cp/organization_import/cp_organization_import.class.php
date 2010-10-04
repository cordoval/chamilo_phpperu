<?php

/**
 * Import a IMS CP Manifest organization node as a learning path.
 *
 *
 * University of Geneva
 *
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpOrganizationImport{

	public static function factory(ImscpManifestReader $item, ObjectImportSettings $settings){
		if($item->is_organization()){
			return new self($item, $settings);
		}else{
			return EmptyObjectImport::get_instance();
		}
	}

	public static function get_learning_path_types(){
		//@todo: should be moved to learning path 
		$result = array(LearningPath :: get_type_name(), 
						Announcement :: get_type_name(), 
						Assessment :: get_type_name(), 
						BlogItem :: get_type_name(), 
						CalendarEvent :: get_type_name(),
						Description :: get_type_name(), 
						Document :: get_type_name(), 
						Forum :: get_type_name(), 
						Glossary :: get_type_name(), 
						Hotpotatoes :: get_type_name(), 
						Link :: get_type_name(),
						Note :: get_type_name(), 
						Wiki :: get_type_name()
						);
		return $result;
	}

	private $settings;
	private $item;

	public function __construct(ImscpManifestReader $item, ObjectImportSettings $settings){
		$this->item = $item;
		$this->settings = $settings;
	}

	/**
	 * @return ObjectImportSettings
	 */
	public function get_settings(){
		return $this->settings;
	}

	/**
	 * @return ImscpManifestReader
	 */
	public function get_item(){
		return $this->item;
	}

	public function import_content_object(){
		$item = $this->get_item();
		$settings = $this->get_settings();
		$title = $item->get_title()->value();
		if(empty($title)){
			$file = $settings->get_path();
			$name = $file['name'];
			$title = preg_replace('/\..*/', '', $name);
		}
		$object = new LearningPath(array(), array());
		$object->set_title($title);
		$object->set_description($title);
		$object->set_owner_id($settings->get_user()->get_id());
		$object->set_parent_id($settings->get_category());
		$object->set_state(ContentObject::STATE_NORMAL);
		$object->save();

		$items = $item->list_item();
		foreach($items as $item){
			$this->import_item($object, $item, $settings);
		}
	}

	protected function import_item(LearningPath $object, ImscpManifestReader $item, ObjectImportSettings $settings){
		$title = $item->get_title()->value();
		$resource = $item->navigate();
		$href = $resource->href;
		$href = empty($href) ? $resource->first_file()->href : $href;
		$type = $resource->type;
		$child_object = null;
		if(!empty($href)){
			$resource_settings = $settings->copy($settings->get_directory().$href, $type);
			if($child_object = CpImport::object_factory($resource_settings)->import_content_object()){
				$types = self::get_learning_path_types();
				if(	$child_object instanceof ContentObject && 
					in_array($child_object->get_type(), $types)){
					$child = new LearningPathItem();
					$child->set_reference($child_object->get_id());
					$child->save();
	
					$cloi = new ComplexLearningPathItem();
					$cloi->set_ref($child->get_id());
					$cloi->set_user_id($settings->get_user()->get_id());
					$cloi->set_parent($object->get_id());
					$cloi->set_display_order(ContentObject::get_data_manager()->select_next_display_order($object->get_id()));
					$cloi->save();
				}
			}
		}
		$count=0;
		$children_item = $item->list_item();
		$process_children = count($children_item)>0 && 
							(empty($child_object) || ($child_object instanceof ContentObject && !$child_object->is_complex_content_object()));
		if($process_children){
			$title = empty($title) ? $object->get_title() . '.' . ++$count : $title;
			$child = new LearningPath(array(), array());
			$child->set_title($title);
			$child->set_description($title);
			$child->set_owner_id($settings->get_user()->get_id());
			//$child->set_parent_id($object->get_id());
			$object->set_state(ContentObject::STATE_NORMAL);
			$child->save();
				
			$cloi = new ComplexLearningPathItem();
			$cloi->set_ref($child->get_id());
			$cloi->set_user_id($settings->get_user()->get_id());
			$cloi->set_parent($object->get_id());
			$cloi->set_display_order(ContentObject::get_data_manager()->select_next_display_order($object->get_id()));
			$cloi->save();
			foreach($children_item as $child_item){
				$this->import_item($child, $child_item, $settings);
			}
		}
	}

}





?>