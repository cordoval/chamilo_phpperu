<?php

require_once 'main.php';

/**
 * Import an IMS CP package's content.
 *
 * Import looks into the package's content and tries to import the package's items.
 * It does not import the package as whole.
 *
 *
 * @copyright (c) 2010 University of Geneva
 *
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpImport extends ContentObjectImport{

	private $settings = null;

	public function __construct($file, $user, $category, $log = NULL){
		parent::__construct($file, $user, $category);
		$ext = strpos(strtolower($file['type']), 'zip') !== false ? 'zip' : '';
		$path = $file['tmp_name'];
		$name = str_replace(".$ext", '', $file['name']);
		$this->settings = new ObjectImportSettings($path, $name, $ext, $user, $category, $log);
	}

	/**
	 * @return ObjectImportSettings
	 */
	public function get_settings(){
		return $this->settings;
	}

	public function import_content_object(){
		$settings = $this->get_settings();

		$importer = CpObjectImportBase::factory();
		$result = $importer->import($settings);

		$log = $settings->get_log();
		$this->add_messages($log->get_messages());
		$this->add_warnings($log->get_warnings());
		$this->add_errors($log->get_errors());
		return $result;
	}

	/**
	 * Publish a content object to a course.
	 *
	 * @param Course $course
	 * @param ContentObject $object
	 */
	public function publish(Course $course, $object){
		$objects = is_array($object) ? $object : array($object);
		$settings = $this->get_settings();
		$user = $settings->get_user();
		$application = Application::factory('Weblcms', $user);
		foreach($objects as $object){
			if($tool = $this->get_tool_name($application, $course, $object)){
				$pub = new ContentObjectPublication();
				$pub->set_course_id($course->get_id());
				$pub->set_content_object_id($object->get_id());
				$pub->set_tool($tool);
				$pub->set_hidden(false);
				$pub->set_publisher_id($user->get_id());
				$pub->set_parent_id(0);
				$pub->set_category_id(0);
				$pub->set_from_date(0);
				$pub->set_to_date(0);
				$time = time();
				$pub->set_publication_date($time);
				$pub->set_modified_date($time);
				$pub->save();
			}
		}
	}

	/**
	 * Returns the tool name used to publish a content object to a course.
	 *
	 * @param $application
	 * @param Course $course
	 * @param ContentObject $object
	 */
	protected function get_tool_name($application, Course $course, ContentObject $object){
		$tools_properties = $course->get_tools();
		foreach($tools_properties as $tool_properties){
			$tool = Tool::factory($tool_properties->name, $application);
            $allowed_types = $tool->get_allowed_types();
            if (in_array($object->get_type(), $allowed_types)){
            	return $tool_properties->name;
            }
		}
		return NULL;
	}


/*
	protected function import_resources($resources){
		foreach($resources as $resource){
			$this->import_resource($resource);
		}
	}

	protected function import_resource(ImscpManifestReader $resource){
		$resource->get_resources();
		$type = $resource->get_type();
		$metadata = $resource->has_metadata ? $resource->get_metadata() : null;
		$href = $resource->href;
		$href = empty($href) ? $resource->first_file()->href : $href;
		$path = $this->get_directory() . $href;
		$resource_settings = $this->settings->copy($path, $type, $metadata);
		return self::object_factory($resource_settings)->import_content_object();
	}

	protected function import_organizations($organizations){
		foreach($organizations as $organization){
			CpOrganizationImport::factory($organization, $this->get_settings())->import_content_object();
		}
	}*/

	public function __call($name, $arguments){
		return call_user_func_array(array($this->settings, $name), $arguments);
	}



}














?>