<?php

require_once dirname(__FILE__) .'/question_builder.class.php';
require_once_all(dirname(__FILE__) .'/builder/*.class.php');

/**
 * Base class for all object builders.  
 * Builders are responsible to construct a chamilo question object.
 * Relies on the import strategies to extract values from the QTI file and on the QTI renderer
 * to render the object's parts. 
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class BuilderBase{
	
	/**
	 * @param ImsQtiReader $item
	 * @return QuestionBuilder
	 */
	static function factory($item, $source_root, $target_root, $category, $user, $object_factory, $log){
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
		debug('not found: ');
		debug($item->get_current());
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
	private $category = 0;
	private $user = null;
	private $object_factory = null;
	private $log = null;

	public function __construct($source_root, $target_root, $category, $user, $object_factory=null, $log){
		$resource_manager = new QtiImportResourceManager($source_root, $target_root);
		$renderer = new QtiPartialRenderer($resource_manager);
		$this->strategy = QtiImportStrategyBase::create_moodle_default_strategy($renderer);
		$this->user = $user;
		$this->category = $category;
		$this->object_factory = $object_factory;
		$this->log = $log;
	}

	/**
	 * @return QtiImportStrategy
	 */
	public function get_strategy(){
		return $this->strategy;
	}
	
	public function get_category(){
		return $this->category;
	}
	
	public function get_user(){
		return $this->user;
	}
	
	public function get_log(){
		return $this->log;
	}
	
	public function get_object_factory(){
		return $this->object_factory;
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
	
	public function get_source_root(){
		return $this->get_resource_manager()->get_source_root();
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
	
	public function __call($name, $arguments) {
		$f = array($this->strategy, $name);
		if(is_callable($f)){
			return call_user_func_array($f, $arguments);
		}else{
			throw new Exception('Unknown method: '. $name);
		}
	}
}











