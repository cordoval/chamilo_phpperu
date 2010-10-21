<?php

/**
 * Returns objects matching a keyword search.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_search extends fedora_fs_folder{

	const SEARCH_LEVEL_EXACT = 1;
	const SEARCH_LEVEL_FUZZI = 2;
	const SEARCH_LEVEL_REGEX = 3;

	public function __construct($title='', $term, $search_level=self::SEARCH_LEVEL_FUZZI, $start_date= NULL, $end_date = NULL, $owner = NULL, $sort='' , $hitPageSize=0, $offset=0){
		if($title){
			$this->title = $title;
		}
		if($term){
			$this->term = $term;
		}
		if($search_level){
			$this->search_level = $search_level;
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
		if($hitPageSize){
			$this->hitPageSize = $hitPageSize;
		}
		if($offset){
			$this->offset = $offset;
		}
		if($sort){
			$this->sort = $sort;
		}
	}

	public function get_term(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_search_level(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_end_date(){
		return $this->get(__FUNCTION__, endoftime());
	}

	public function get_start_date(){
		return $this->get(__FUNCTION__, 0);
	}

	public function get_owner(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_hitPageSize(){
		return $this->get(__FUNCTION__, self::get_max_results());
	}

	public function get_offset(){
		return $this->get(__FUNCTION__, 0);
	}

	public function get_sort(){
		return $this->get(__FUNCTION__, '');
	}

	public function query(FedoraProxy $fedora, $sort=false, $limit=false, $offset=false){
		$result = array();

		$term = $this->get_term();
		$search_level = $this->get_search_level();
		$start = $this->get_start_date();
		$end = $this->get_end_date();
		$owner = $this->get_owner();
		$hit = $this->get_hitPageSize();
		$offset = $this->get_offset();
		$sort = $this->get_sort();

		$objects = self::sparql_find($fedora, $term, $search_level, $start, $end, $owner, $sort, $hit, $offset);
		foreach($objects as $object){
			$pid = $object['pid'];
			$label = $object['label'];
			$mdate = $object['lastmodifieddate'];
			$cdate = $object['createdDate'];
			$owner = $object['ownerId'];
			$result[] = new fedora_fs_object($pid, $label, $owner, $cdate, $mdate);
		}
		return $result;
	}

	public function count(FedoraProxy $fedora){
		$result = array();

		$term = $this->get_term();
		$search_level = $this->get_search_level();
		$start = $this->get_start_date();
		$end = $this->get_end_date();
		$owner = $this->get_owner();
		$hit = $this->get_hitPageSize();
		$offset = $this->get_offset();
		$sort = $this->get_sort();

		$result = self::sparql_count($fedora, $term, $search_level, $start, $end, $owner, $hit, $offset, $sort);
		return $result;
	}


}

