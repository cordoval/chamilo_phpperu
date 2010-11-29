<?php
namespace repository;

use common\libraries\StringUtilities;
use common\libraries\Utilities;

/**
 * Base class for all object builders.
 * Builders are responsible to construct a chamilo question object.
 * Relies on the import strategies to extract values from the QTI file and on the QTI renderer
 * to render the object's parts.
 *
 * @copyright (c) 2010 University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiBuilderBase{

	/**
	 * @param ImsQtiReader $item
	 * @return QtiQuestionBuilder
	 */
	public static function factory($item, $settings){
		$args = func_get_args();
		$directory = dirname(__FILE__).'/builder/';
		$files = scandir($directory);
		foreach($files as $file){
			if(strlen($file)>=3 && StringUtilities::end_with($file, 'php', false)){
				require_once $directory . $file;
				$class = str_replace('.class.php', '', $file);
				$class = Utilities::underscores_to_camelcase($class);
				$f = array($class, 'factory');
				if(is_callable($f) &&  $result = call_user_func_array($f, $args)){
					return $result;
				}
			}
		}
		return null;
	}

	public static function has_score($item){
		return QtiImportStrategyBase::has_score($item);
	}

	/**
	 * Returns the tool name used to generate qti files.
	 * Mostly used to identify if a file is a reimport.
	 */
	public static function get_tool_name(){
		return Qti::get_tool_name('chamilo');
	}

	/**
	 *
	 * @var QtiImportStrategy
	 */
	private $strategy = null;
	private $settings = null;

	public function __construct(ObjectImportSettings $settings){
		$directory = $settings->get_directory();
		$resource_manager = new QtiImportResourceManager($directory, '');
		$renderer = new QtiPartialRenderer($resource_manager);
		$this->strategy = QtiImportStrategyBase::create_moodle_default_strategy($renderer);
		$this->settings = $settings;
	}

	/**
	 * @return QtiImportStrategy
	 */
	public function get_strategy(){
		return $this->strategy;
	}

	/**
	 * @return ObjectImportSettings
	 */
	public function get_settings(){
		return $this->settings;
	}

	/**
	 * @return QtiResourceManager
	 */
	public function get_resource_manager(){
		return $this->strategy->get_renderer()->get_resource_manager();
	}

	public function get_resources(){
		return $this->get_resource_manager()->get_resources();
	}

	public function get_user(){
		return $this->get_settings()->get_user();
	}

	public function get_category(){
		return $this->get_settings()->get_category_id();
	}

	public function get_directory(){
		return $this->get_settings()->get_directory();
	}

	/**
	 *
	 * @param ImsQtiReader $item
	 */
	public function build($item){
		return null;
	}

	public function to_html($item){
		$result = $this->get_strategy()->to_html($item);
		$result = $this->translate_images($result);
		return $result;
	}

	protected function translate_images($text){
		$tags = Text::fetch_tag_into_array($text, '<img>');
		$tags = empty($tags) ? array() : $tags;
		$file_path = $this->get_settings()->get_directory();

		$translate = array();
		foreach($tags as $tag){
			$src = $tag['src'];
			if(substr($src, 0, 4) !== 'http' ){
				if(! isset($translate[$src]) ){
					$path = $file_path . $tag['src'];
					$doc = $this->create_document($path);
					$new_src = 'core.php?go=document_downloader&display=1&object='. $doc->get_id() .'&application=repository';
					$translate[$src] = $new_src;
				}
				$text = str_replace($src, $translate[$src], $text);
			}
		}
		return $text;
	}

	protected function get_feedback(ImsQtiReader $item, ImsQtiReader $interaction, $answer, $filter_out = array()){
		$result =  $this->get_feedbacks($item, $interaction, $answer, $filter_out);
		$result =  implode('<br/>', $result);
		return $result;
	}

	protected function create_document($path){
		$owner_id = $this->get_user()->get_id();
		$category = $this->get_category();
		$ext = end(explode('.', $path));
		$title = basename($path, ".$ext");

		if(! is_file($path)){//i.e. the file has already been imported. Note that creating a Document remove the temp file.
			$conditions[] = new EqualityCondition(ContentObject::PROPERTY_TITLE, $title);
			$conditions[] = new EqualityCondition(ContentObject::PROPERTY_OWNER_ID, $owner_id);
			$conditions[] = new EqualityCondition(ContentObject::PROPERTY_PARENT_ID, $category);
			$condition = new AndCondition($conditions);

			$objects = RepositoryDataManager::get_instance()->retrieve_content_objects($condition);
			$result = $objects->is_empty() ? null : $objects->next_result();
		}else{
			$result = new Document();
			$result->set_owner_id($owner_id);
			$result->set_parent_id($category);
			$result->set_temporary_file_path($path);
			$result->set_filename($title . '.' . $ext);
			$result->set_filesize(Filesystem::get_disk_space($path));
			$result->set_hash(md5($title));
			$result->set_description($title);
			$result->set_title($title);
			$result->create();
		}
		return $result;
	}

	protected function get_instruction(ImsQtiReader $item, $role = Qti::VIEW_ALL){
		$result = $this->get_rubricBlock($item, $role);
		$result = implode('<br/>', $result);
		return $result;
	}

	/**
	 * Ensure a message is only recorded once.
	 *
	 * @param $message
	 */
	protected function log_error($message){
		static $messages = array();
		if(isset($messages[$message])){
			return false;
		}else{
			$messages[$message] = $message;
			$log = $this->get_settings()->get_log();
			$log->error($message);
		}
	}

	public function __call($name, $arguments) {
		$f = array($this->strategy, $name);
		if(is_callable($f)){
			return call_user_func_array($f, $arguments);
		}
		$f = array($this->settings, $name);
		if(is_callable($f)){
			return call_user_func_array($f, $arguments);
		}
		throw new Exception('Unknown method: '. $name);
	}
}