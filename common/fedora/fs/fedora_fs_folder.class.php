<?php

/**
 * Base class for folder FS objects. That is objects that returns other FS objects.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_folder extends fedora_fs_base{

	static function sparql_find($fedora, $search='', $searchLevel=2, $start_date = NULL, $end_date = NULL, $owner='', $sort='', $hitPageSize=0, $offset=0){
		$query = new fedora_fs_sparql_query();
		$query->search = $search;
		$query->searchLevel = $searchLevel;
		$query->start_date = $start_date;
		$query->end_date = $end_date;
		$query->owner = $owner;
		$query->hitPageSize = $hitPageSize;
		$query->offset = $offset;
		$query->sort = $sort;
		return $query->query($fedora);
	}

	static function sparql_count($fedora, $search='', $searchLevel=2, $start_date = NULL, $end_date = NULL, $owner='', $sort='', $hitPageSize=0, $offset=0){
		$query = new fedora_fs_sparql_query();
		$query->search = $search;
		$query->searchLevel = $searchLevel;
		$query->start_date = $start_date;
		$query->end_date = $end_date;
		$query->owner = $owner;
		$query->hitPageSize = $hitPageSize;
		$query->offset = $offset;
		$query->sort = $sort;
		return $query->count($fedora);
	}

	static function itql_find($fedora, $start_date = null, $end_date = null, $owner='', $state_text = 'Active', $sort=false, $hitPageSize=false, $offset=false){
		$query = new fedora_fs_itql_query();
		$query->start_date = $start_date;
		$query->end_date = $end_date;
		$query->owner = $owner;
		$query->hitPageSize = $hitPageSize;
		$query->offset = $offset;
		$query->sort = $sort;
		$query->state_text = $state_text;
		return $query->query($fedora);
	}

	static function itql_count($fedora, $start_date = null, $end_date = null, $owner='', $sort=false, $hitPageSize=false, $offset=false){
		$query = new fedora_fs_itql_query();
		$query->start_date = $start_date;
		$query->end_date = $end_date;
		$query->owner = $owner;
		$query->hitPageSize = $hitPageSize;
		$query->offset = $offset;
		$query->sort = $sort;
		return $query->count($fedora);
	}

	public function query(FedoraProxy $fedora, $sort=false, $limit=false, $offset=false){
		return array();
	}

	public function get_thumbnail(){
		$default = $default = self::$resource_base . 'folder.png';
		return $this->get(__FUNCTION__, $default);
	}

	/**
	 * @return The html class to be used for this object
	 */
	public function get_class(){
		return $this->get(__FUNCTION__, 'fedora_folder');
	}

	public function format($path = array()){
		$result = array();
		$title = $this->get_title();
		$source = $this->get_source();
		$date = $this->get_date();
		$size = $this->get_size();
		$thumbnail = $this->get_thumbnail();
		if(!empty($title)){
			$result = array(
		        		'title' => $title,
						'shorttitle' => $title,
		        		'date'=> $date,
		        		'size'=> $size,
		        		'thumbnail' => $thumbnail,
						'children' =>array(),
						'path' => $this->get_path($path),
			);
		}
		return $result;
	}

	public function sort(&$items){
		usort($items, array($this, 'compare'));
		return $items;
	}

	protected function compare($left, $right){
		$wa = strtolower($left->get_date());
		$wb = strtolower($right->get_date());
		if ($wa == $wb) {
			$result =  0;
		}else{
			$result = ($wa > $wb) ? -1 : 1;
		}
		return $result;
	}
}

