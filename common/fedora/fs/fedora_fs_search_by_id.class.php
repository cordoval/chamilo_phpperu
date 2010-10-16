<?php

/**
 * Returns object based on his pid.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_search_by_id extends fedora_fs_folder{

	public function __construct($pid, $title=''){
		if($title){
			$this->title = $title;
		}
		if($pid){
			$this->pid = $pid;
		}
	}

	public function get_pid(){
		return $this->get(__FUNCTION__, '');
	}

	public function query(FedoraProxy $fedora, $sort=false, $limit=false, $offset=false){
		$result = array();

		if($object = $fedora->get_object_profile($this->pid)){
			$pid = $this->pid;
			$label = $object[strtolower('objlabel')];
			$mdate = $object[strtolower('objLastModDate')];
			$cdate = $object[strtolower('objCreateDate')];
			$owner = $object[strtolower('objOwnerId')];
			$result[] = new fedora_fs_object($pid, $label, $owner, $mdate, $cdate);
		}else{
			$result = false;
		}
		return $result;
	}
}

