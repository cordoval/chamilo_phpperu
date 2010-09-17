<?php

require_once Path::get_repository_path() .'lib/import/qti/main.php';

/**
 * Import IMS QTI files. Delegate the work to the QTI import module.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiCpImport extends CpObjectImportBase{

	public function get_extentions(){
		return array('xml');
	}

	public function accept($settings){
		$type = strtolower($settings->get_type());
		if($type == 'imsqti_item_xmlv2p1' || $type == 'imsqti_item_xmlv2p0'){
			return true;
		}else if(empty($type) && Qti::is_qti_file($settings->get_path())){
			return new self($settings);
		}else{
			return false;
		}
	}

	protected function process_import(ObjectImportSettings $settings){
    	$result = QtiImport::object_factory($settings)->import_content_object();
    	if($result){
    		$this->log_warning($settings, 'QtiImportWarning');
    	}
        return $result;
    }


	/**
	 * Log a warning  message. Ensure the same message is not sent twice.
	 * The problem is that some importers - zip - recall 'import' on the root after they have performed their work.
	 * This causes the tree of importers to be traversed twice, hence resulting on duplicate messages without this security.
	 *
	 * @param ObjectImportSettings $settings
	 */
	protected function log_warning($settings, $var){
		static $messages = array();
		$message = Translation::translate($var);
		if(isset($messages[$message])){
			return false;
		}else{
			$messages[$message] = $message;
			$settings->get_log()->warning($message);
			return true;
		}
	}



}

?>