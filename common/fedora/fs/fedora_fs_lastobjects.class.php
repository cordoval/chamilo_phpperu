<?php

/**
 * Returns the last N modified objects belonging to the current user
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_lastobjects extends fedora_fs_folder{

	const DEFAULT_LIMIT = 10;

	public function __construct($title = '', $limit = 0, $owner='', $fsid = ''){
		parent::__construct($fsid);
		if($limit){
			$this->limit = $limit;
		}
		if($title){
			$this->title = $title;
		}
		if($owner){
			$this->owner = $owner;
		}
	}

	public function get_title(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_owner(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_limit(){
		return $this->get(__FUNCTION__, self::DEFAULT_LIMIT);
	}

	public function query_string($sort=false, $limit=false, $offset=false){
		$limit = $limit ? $limit : $this->get_limit();
		$limit = min($this->get_limit(), $limit);
		$sort = $sort ? $sort : '$modified desc';
		$result = 'select $pid $modified $label $ownerId $created from <#ri> ';
		$result .= 'where( ';
		$result .= '$pid <fedora-model:hasModel> <info:fedora/fedora-system:FedoraObject-3.0> ';
		$result .= 'and $pid <fedora-view:lastModifiedDate> $modified ';
		$result .= 'and $pid <fedora-model:label> $label ';
		$result .= 'and $pid <fedora-model:ownerId> $ownerId ';
		$result .= 'and $pid <fedora-model:createdDate> $created ';
		if($owner = $this->get_owner()){
			$result .= 'and $pid <fedora-model:ownerId> \''. $this->get_owner() . '\' ';
		}
		$result .= ')minus(' ;
		$result .= '$pid <fedora-rels-ext:isCollection> \'true\' ';
		$result .= ') ';
		if($sort){
			$result .= 'order by ' . $sort . ' ';
		}
		if($limit){
			$result .= 'limit '. $limit . ' ';
		}
		if($offset){
			$result .= 'offset '.$offset . ' ';
		}
		return $result;
	}

	public function query($fedora, $sort=false, $limit=false, $offset=false){
		$result = array();

		$query = $this->query_string($sort, $limit, $offset);
		$objects = $fedora->ri_search($query, '', 'tuples', 'iTql', 'Sparql');
		foreach($objects as $object){
			$pid = str_replace('info:fedora/', '', $object['pid']['@uri']);
			$label = $object['label'];
			$owner = $object['ownerid'];
			$cdate = $object['created'];
			$mdate = $object['modified'];
			$result[] = new fedora_fs_object($pid, $label, $owner, $mdate, $cdate);
		}
		return $result;
	}

	public function count($fedora){
		$query = $this->query_string();
		return $fedora->ri_search($query, '', 'tuples', 'iTql', 'count');
	}
}









