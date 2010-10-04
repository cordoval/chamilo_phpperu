<?php

/**
 * Export course. Including groups, users and publications.
 * Users and groups are exported in the course xml file.
 * Publications as exported as separate files. 
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpCourseExportData extends CpeObjectExportBase{

	public static function factory($settings){
		$object = $settings->get_object();
		if($object instanceof Course){
			return new self($settings);
		}else{
			return null;
		}
	}

	public function get_type(){
		$result = ImscpObjectWriter::get_format_full_name() . '#Course';
		return $result;
	} 
	
	protected function add_object(ImscpObjectWriter $writer, DataClass $object){
		$catalog = chamilo::get_local_catalogue_name();
		$id = $object->get_id();
		$type = $this->get_object_type($object);
		$writer = $writer->get_objects()->add_object($catalog, $id, $type);

		$this->add_identifiers($writer, $object);
		$this->add_default_properties($writer, $object);
		$this->add_course_type($writer, $object);
		$this->add_categories($writer, $object);
		$this->add_settings($writer, $object);
		$this->add_layout_settings($writer, $object);
		$this->add_rights($writer, $object);
		$this->add_intro($writer, $object);
		$this->add_sections($writer, $object);
		$this->add_publications($writer, $object);
		$this->add_groups($writer, $object);
		$this->add_users($writer, $object);
	}

	protected function add_publications(ImscpObjectWriter $writer, $object){
		if(!$object instanceof Course){
			return;
		}
			
		$children = chamilo::get_course_publications($object->get_id());
		$publications = (count($children)> 0) ? $writer->add_publications() : null;
		foreach($children as $child){
			$properties = $child->get_default_properties();
			$child_object = $child->get_content_object();
			$properties[ContentObject::PROPERTY_TITLE] = $child_object->get_title();
			//$properties[ContentObject::PROPERTY_DESCRIPTION] = $child_object->get_description();
			if($path = $this->get_local_file_path($writer, $child_object)){
				$properties[Document::PROPERTY_PATH] = $path;
			}
			$properties = $this->format_properties($properties);
			$ref = $child_object->get_id();
			$id = $child->get_id();
			$href = $this->export_child($child_object);
			$general = $publications->add_publication($href, $id, $ref)->add_general();
			foreach($properties as $name => $value){
				$general->add($name)->add_xhml($value);
			}
		}
	}

	protected function add_users(ImscpObjectWriter $writer, DataClass $object){
		if(!$object instanceof Course){
			return;
		}

		$children = chamilo::get_course_user_relations($object->get_id());
		$relations = (count($children)> 0) ? $writer->add_user_relations() : null;
		foreach($children as $child){
			$child_object = User::get_data_manager()->retrieve_user($child->get_user());
			$course_id = $child->get_course();
			$user_id = $child->get_user();
			$relation = $relations->add_user_relation($course_id, $user_id, $child_object->get_email(), $child_object->get_username());
			$general = $relation->add_general();

			$properties = $child->get_default_properties();
			$properties = $this->format_properties($properties);
			$properties['status_description'] = $child->get_status() == 1 ? 'admin' : 'student';
			foreach($properties as $name => $value){
				$general->add($name)->add_xml($value);
			}

			$user = $relation->add_user($user_id);
			$properties = $child_object->get_default_properties();
			$properties = $this->format_properties($properties);
			$general = $user->add_general();
			foreach($properties as $name => $value){
				$general->add($name)->add_xml($value);
			}
		}
	}

	protected function add_sections(ImscpObjectWriter $writer, DataClass $object){
		$store = CourseSection::get_data_manager();
		$condition = new EqualityCondition(CourseSection::PROPERTY_COURSE_CODE, $object->get_id());
		$course_sections = $store->retrieve_course_sections($condition);
		$sections = $writer->add_sections();
		$modules = $store->get_course_modules($object->get_id());
		while($course_section = $course_sections->next_result()){
			$section = $sections->add_section($course_section->get_id());
			$this->add_default_properties($section, $course_section);
			$modules_writer = $section->add_modules();
			foreach($modules as $module){
				if($module->section == $course_section->get_id()){
					$module_writer = $modules_writer->add_module($module->id, $module->name);	
				}
			}
			
		}
	}

	protected function add_groups(ImscpObjectWriter $writer, DataClass $object){
		$manager = WeblcmsDataManager::get_instance();
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseGroup::PROPERTY_COURSE_CODE, $object->get_id());
		$conditions[] = new EqualityCondition(CourseGroup::PROPERTY_PARENT_ID, 0);
		$condition = new AndCondition($conditions);
		$groups = $manager->retrieve_course_groups($condition);

		$writer = $writer->add_groups();
		while($group = $groups->next_result()){
			$this->add_group($writer, $group, $object);
		}
	}

	protected function add_group(ImscpObjectWriter $writer, CourseGroup $group, $object){
		$writer = $writer->add_group($group->get_id());
		
		$this->add_default_properties($writer, $group);

		$subgroups_writer = $writer->add_groups();
		$subgroups = $group->get_children(false);
		while($subgroup = $subgroups->next_result()){
			$this->add_group($subgroups_writer, $subgroup, $object);
		}
		$members_writer = $writer->add_user_relations();
		$members = $group->get_members();
		while($member = $members->next_result()){
			$this->add_member($members_writer, $member, $group);
		}
	}

	protected function add_member(ImscpObjectWriter $writer, User $user, $group){
		$group_id = $group->get_id();
		$user_id = $user->get_id();
		$relation = $writer->add_user_relation($group_id, $user_id, $user->get_email(), $user->get_official_code());
		/*
		$general = $relation->add_general();

		$properties = $user->get_default_properties();
		$properties = $this->format_properties($properties);
		$properties['status_description'] = $child->get_status() == 1 ? 'admin' : 'student';
		foreach($properties as $name => $value){
			$general->add($name)->add_xml($value);
		}*/
			
		$properties = $user->get_default_properties();
		$properties = $this->format_properties($properties);
		$general = $relation->add_user($user_id)->add_general();
		foreach($properties as $name => $value){
			$general->add($name)->add_xml($value);
		}
	}
	
	protected function add_settings(ImscpObjectWriter $writer, Course $object){
		$settings = $object->get_settings();
		$writer = $writer->add_settings();
		$this->add_default_properties($writer, $settings);
	}
	
	protected function add_layout_settings(ImscpObjectWriter $writer, Course $object){
		$settings = $object->get_layout_settings();
		$writer = $writer->add_layout_settings();
		$this->add_default_properties($writer, $settings);
	}
	
	protected function add_rights(ImscpObjectWriter $writer, Course $object){
		$rights = $object->get_rights();
		$writer = $writer->add_rights();
		$this->add_default_properties($writer, $rights);
	}
	
	protected function add_intro(ImscpObjectWriter $writer, Course $object){
		if(!$object->get_intro_text()){
			return ;
		}

		$conditions = array();
		$conditions[] = new EqualityCondition(ContentObjectPublication::PROPERTY_COURSE_ID, $object->get_id());
		$conditions[] = new EqualityCondition(ContentObjectPublication::PROPERTY_TOOL, 'introduction');
		$condition = new AndCondition($conditions);

		$publications = WeblcmsDataManager::get_instance()->retrieve_content_object_publications_new($condition);

		if ($introduction_text = $publications->next_result()){
			$writer->add_introduction($introduction_text->get_content_object()->get_description());
		}
	}
	
	protected function add_course_type(ImscpObjectWriter $writer, Course $object){
		$course_type = $object->get_course_type();
		$writer = $writer->add_type($course_type->get_id(), $course_type->get_name());
		$this->add_default_properties($writer->add_settings(), $course_type->get_settings());
		$this->add_default_properties($writer->add_layout_settings(), $course_type->get_layout_settings());
		$tools_writer = $writer->add_modules();
		$tools = $course_type->get_tools();
		foreach($tools as $tool){
			$tool_writer = $tools_writer->add_module('', $tool->get_name());
			$this->add_default_properties($tool_writer, $tool);
		}
		$rights_writer = $writer->add_rights();
		$rights = $course_type->get_rights();
		$this->add_default_properties($rights_writer, $rights);
	}
	
	protected function add_categories(ImscpObjectWriter $writer, Course $object){
		$db = WeblcmsDataManager::get_instance();
        $category = $db->retrieve_course_category($object->get_category());
        if(!empty($category)){
        	$writer = $writer->add_categories();
        	$writer->add_category($category->get_id(), $category->get_name());
        }
	}

}


















?>