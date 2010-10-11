<?php

/**
 * Returns object belonging to the current user.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_mystuff extends fedora_fs_folder{

	public function __construct($fsid = '', $owner){
		parent::__construct($fsid);
		$this->owner = $owner;
	}

	/**
	 * @return The html class to be used for this object
	 */
	public function get_class(){
		return $this->get(__FUNCTION__, 'home');
	}

	public function get_owner(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_title(){
		return $this->translate('mystuff');
	}

	public function query($fedora, $sort=false, $limit=false, $offset=false){
		$result = array();

		$owner = $this->get_owner();
		if($limit){
			$limit = min(self::$max_results, (int)$limit);
		}
		$objects = self::itql_find($fedora, null, null, $owner, $sort, $limit, $offset);
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

	public function count($fedora){
		$owner = $this->get_owner();
		$result = self::sparql_count($fedora, '', 0, NULL, NULL, $owner, self::$max_results);
		return $result;
	}
}

