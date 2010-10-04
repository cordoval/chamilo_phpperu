<?php

require_once dirname(__FILE__) .'/question_builder.class.php';
require_once_all(dirname(__FILE__) .'/builder/*.class.php');

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
class BuilderBase{

	/**
	 * @param ImsQtiReader $item
	 * @return QuestionBuilder
	 */
	static function factory($item, $settings){
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
		//debug('not found: ');
		//debug($item->get_current());
		return null;
	}

	static function has_score($item){
		return QtiImportStrategyBase::has_score($item);
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
        $file_path = $this->get_source_root();
        $target_directory = Path::get(SYS_PATH) . 'files/repository/' . $this->get_user()->get_id() . '/';

        //@todo: check image import when question works. See if we import the image as document object or not.
        $files = array();
        foreach($tags as $tag){
            $filename = basename($tag['src']);
            $files[$filename] = $file_path .$tag['src'];
            $text = str_replace($tag['src'], $filename, $text);
        }
        foreach($files as $new => $original){
        	$from = $original;
        	$to = $target_directory.$new;
	    	$result = Filesystem::copy_file($from, $to, true);
        }
        return $text;
    }

	protected function get_feedback(ImsQtiReader $item, ImsQtiReader $interaction, $answer, $filter_out = array()){
		$result =  $this->get_feedbacks($item, $interaction, $answer, $filter_out);
		$result =  implode('<br/>', $result);
		return $result;
	}

	protected function create_document($path){
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$name = basename($path, ".$ext");
    	$result = new Document();
    	$result->set_owner_id($this->get_user()->get_id());
    	$result->set_parent_id($this->get_category());
    	$result->set_temporary_file_path($path);
    	$result->set_filename($name);
    	$result->set_filesize(Filesystem::get_disk_space($path));
    	$result->set_hash(md5($name));
    	$result->set_description($name);
    	$result->set_title($name);
    	$sucess = $result->create();
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











