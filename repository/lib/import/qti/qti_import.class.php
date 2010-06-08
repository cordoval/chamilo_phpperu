<?php

require_once dirname(__FILE__) . '/main.php';

class QtiImport extends ContentObjectImport{
	
	/**
	 * 
	 * @var Log
	 */
	private $log = null;
	
	public function __construct($content_object_file, $user, $category, $log = null){
		parent::__construct($content_object_file, $user, $category);
		$this->log = empty($log) ? new Log() : $log;
	}

    public function import_content_object(){
    	$result = false;
        $zipper = Filecompression::factory();
        $temp = $zipper->extract_file($this->get_content_object_file_property('tmp_name'));
        
        $directory = "$temp/";
        if(!file_exists($directory)){
        	return false;
        }
        
        $user = $this->get_user();
        $category = $this->get_category();
        $factory = new BufferedFactory();
		$files = $this->get_files($directory);
        foreach($files as $path){
        	$import_result = $factory->import($path, $user, $category, $this->log);
        	$result = $result ? true : !empty($import_result);
        }
        
        if($temp){
        	Filesystem::remove($temp);
        }
        $this->log->translate('QtiImportWarning', Log::TYPE_WARNING);
        $this->add_messages($this->log->get_messages());
        $this->add_warnings($this->log->get_warnings());
        $this->add_errors($this->log->get_errors());
        return $result;
    }
    
    protected function get_files($directory){
    	$files = Filesystem::get_directory_content($directory, Filesystem::LIST_FILES, false);
    	$assessments = array();
    	$questions = array();
    	foreach($files as $file){
    		if(Qti::is_test_file($directory.$file)){
    			$assessments[] = $directory.$file;
    		}else if(Qti::is_question_file($directory.$file)){
    			$questions[] = $directory.$file;
    		}
    	}
    	//assessments should be imported first
    	return array_merge($assessments, $questions);
    }
}
