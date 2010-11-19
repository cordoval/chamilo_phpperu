<?php
namespace common\libraries;

/**
 * Returns objects belonging to a subject
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_subject extends fedora_fs_folder{

	public static function factory($subjects, $owner=''){
		$result = array();
		foreach($subjects as $subject){
			$title = $subject['title'];
			$key = $subject['id'];
			if(isset($subject['sub'])){
				$store = new fedora_fs_store($title, 'st_'. $key);
				$store->aggregate(new self($title, $key, $owner, 'sb_'. $key));
				$children = self::factory($subject['sub']);
				$store->add_all($children);
				$result[] = $store;
			}else{
				$result[] = new self($title, $key, $owner, 'sb_'. $key);
			}
		}
		return $result;
	}

	public function __construct($title = '', $key = '', $owner='', $fsid = ''){
		parent::__construct($fsid);
		if($key){
			$this->key = $key;
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

	public function get_key(){
		return $this->get(__FUNCTION__, '');
	}

	public function query_string($sort=false, $limit=false, $offset=false){
		$key = $this->get_key();
		$owner = $this->get_owner();
		$result = 'select $pid $modified $label $ownerId $created from <#ri> ';
		$result .= 'where( ';
		$result .= '$pid <fedora-model:hasModel> <info:fedora/fedora-system:FedoraObject-3.0> ';
		$result .= 'and $pid <fedora-view:lastModifiedDate> $modified ';
		$result .= 'and $pid <fedora-model:label> $label ';
		$result .= 'and $pid <fedora-model:ownerId> $ownerId ';
		$result .= 'and $pid <fedora-model:createdDate> $created ';
		if($key){
			$result .= 'and $pid <http://purl.org/dc/terms/subject> \''. $key . '\' ';
		}
		if($owner){
			$result .= 'and ($pid <fedora-model:ownerId> \''. $owner . '\' ';
			$result .= ' or $pid <http://purl.org/dc/terms/accessRights> \'public\' ';
			$result .= ' or $pid <http://purl.org/dc/terms/accessRights> \'institution\') ';
		}
		$result .= ')minus(' ;
		$result .= '$pid <fedora-rels-ext:isCollection> \'true\' ';
		if(empty($key)){
			$result .=  ' or $pid <http://purl.org/dc/terms/subject> $subject ';
		}
		$result .= ') ';

		if($sort){
			$result .= 'order by ' . $sort . ' ';
		}
		if($limit){
			$result .= 'limit '.$limit . ' ';
		}
		if($offset){
			$result .= 'offset '.$offset . ' ';
		}
		return $result;
	}

	public function query(FedoraProxy $fedora, $sort=false, $limit=false, $offset=false){
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

	public function count(FedoraProxy $fedora){
		$query = $this->query_string();
		$result =  $fedora->ri_search($query, '', 'tuples', 'iTql', 'count');
		return $result;
	}
}

