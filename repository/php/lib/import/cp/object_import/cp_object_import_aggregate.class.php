<?php

include_once dirname(__FILE__) . '/cp_object_import_base.class.php';

/**
 * Represents an aggregation of several importer. Delegate the calls to the first object that accept the call.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class CpObjectImportAggregate extends CpObjectImportBase{

	private $items = array();

	/**
	 * Add an object importer
	 * @param $item
	 */
	public function add($item){
		$this->items[] = $item;
	}

	/**
	 * Returns the list of object importer making of this object.
	 */
	public function get_items(){
		return $this->items;
	}

	/**
	 * Sort aggregated imported on their weight propery.
	 * Used to set up priority between importers which works on the same file extentions.
	 */
	public function sort(){
		object_sort($this->items, 'get_weight');
	}

	public function get_extentions(){
		$result = array();
		$items = $this->get_items();
		foreach($items as $item){
			$extentions = $item->get_extentions();
			if(!empty($extentions)){
				$result = array_merge($result, $extentions);
			}
		}
		return $result;
	}

	public function accept($settings){
		foreach($this->items as $item){
			if($item->accept($settings)){
				return true;
			}
		}
	}

	public function import(ObjectImportSettings $settings){
		foreach($this->items as $item){
			if($result = $item->import($settings)){
				return $result;
			}
		}
		$this->log_failure($settings);
		return false;
	}

	/**
	 * Log a warning failure message. Ensure the same message is not sent twice.
	 * The problem is that some importers - zip - recall 'import' on the root after they have performed their work.
	 * This causes the tree of importers to be traversed twice, hence resulting on duplicate messages without this security.
	 *
	 * @param ObjectImportSettings $settings
	 */
	protected function log_failure(ObjectImportSettings $settings){
		static $messages = array();
		$message = Translation::translate('unableToImport').': '. $settings->get_filename();
		if(isset($messages[$message])){
			return false;
		}else{
			$messages[$message] = $message;
			$settings->get_log()->error($message);
			return true;
		}
	}
}












