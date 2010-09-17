<?php

require_once dirname(__FILE__) . '/main.php';

class QtiImport extends ContentObjectImport{

	/**
	 * Return single file importer. If no importers are found returns a buffered empty importer.
	 *
	 * @param ObjectImportSettings $settings
	 */
	public static function object_factory(ObjectImportSettings $settings){
		if(!file_exists($settings->get_path())){
			//builders may delete the file after import and cicular reference may force this function to be called twice on the same file.
			$result = EmptyObjectImport::get_instance();
		}else if($import = BuilderImport::factory($settings)){
			$result = $import;
		}else{
			$result = EmptyObjectImport::get_instance();
		}

		//Note: even if we don't have a valid importer we want to record in the buffer that the import failed.
		//Otherwise we may try to reprocess the same file several times due to circular references.
		$result = new BufferedObjectImport($settings, $result);
		return $result;
	}

	private $settings = null;

	public function __construct($content_object_file, $user, $category, $log = null){
		parent::__construct($content_object_file, $user, $category);
		$this->settings = new ObjectImportSettings($content_object_file, '', '', $user, $category, $log);
	}

	/**
	 * @return ObjectImportSettings
	 */
	public function get_settings(){
		return $this->settings;
	}

	public function import_content_object(){
		$result = array();
		$zipper = Filecompression::factory();
		$temp = $zipper->extract_file($this->get_content_object_file_property('tmp_name'));

		$directory = "$temp/";
		//$this->set_directory($directory);
		if(!file_exists($directory)){
			return false;
		}

		$files = $this->get_files($directory);
		foreach($files as $path){
			$object_settings = $this->get_settings()->copy($path);
			if($import_result = self::object_factory($object_settings)->import_content_object()){
				$result[] = $import_result;
			}
		}

		if($temp){
			Filesystem::remove($temp);
		}
		$log = $this->get_log();
		if($result){
			$log->translate('QtiImportWarning', Log::TYPE_WARNING);
		}
		$this->add_messages($log->get_messages());
		$this->add_warnings($log->get_warnings());
		$this->add_errors($log->get_errors());

		$result = $result ? $result : false;
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

	public function __call($name, $arguments){
		return call_user_func_array(array($this->settings, $name), $arguments);
	}

}

?>