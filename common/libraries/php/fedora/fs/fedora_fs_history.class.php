<?php
namespace common\libraries;

/**
 * Returns objects belonging to a specific user which have been modified between two dates .
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_history extends fedora_fs_folder{

	public function __construct($title, $start_date = false, $end_date = false, $owner = false, $fsid = ''){
		parent::__construct($fsid);
		if($title){
			$this->title = $title;
		}
		if($start_date){
			$this->start_date = $start_date;
		}
		if($end_date){
			$this->end_date = $end_date;
		}
		if($owner){
			$this->owner = $owner;
		}
	}

	public function get_thumbnail(){
		$default = $default = self::$resource_base . 'history.png';
		return $this->get(__FUNCTION__, $default);
	}

	/**
	 * @return The html class to be used for this object
	 */
	public function get_class(){
		return $this->get(__FUNCTION__, 'fedora_history');
	}

	public function get_end_date(){
		if(isset($this->end_date)){
			return $this->end_date;
		}else{
			return endoftime();
		}
	}

	public function get_start_date(){
		if(isset($this->start_date)){
			return $this->start_date;
		}else{
			return 0;
		}
	}

	public function get_owner(){
		if(isset($this->owner)){
			return $this->owner;
		}else{
			return '';
		}
	}

	public function query(FedoraProxy $fedora, $sort=false, $limit=false, $offset=false){
		$result = array();
		$start = $this->get_start_date();
		$end = $this->get_end_date();

		$owner = $this->get_owner();
		if($limit){
			$limit = min(self::$max_results, (int)$limit);
		}
		$state_text = $this->get_state_text();
		$objects = self::itql_find($fedora, $start, $end, $owner, $state_text, $sort, $limit, $offset);
		foreach($objects as $object){
			$pid = $object['pid'];
			$label = $object['label'];
			$mdate = $object['modified'];
			$cdate = $object['created'];
			$owner = $object['ownerid'];
			$result[] = new fedora_fs_object($pid, $label, $owner, $mdate, $cdate);
		}
		return $result;
	}

	public function count(FedoraProxy $fedora){
		$result = array();
		$start = $this->get_start_date();
		$end = $this->get_end_date();

		$owner = $this->get_owner();
		return self::sparql_count($fedora, '', 0, $start, $end, $owner, self::get_max_results());
	}
}

