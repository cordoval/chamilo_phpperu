<?php
namespace common\libraries;

/**
 * Helper class for generating the text representation of a ITQL query for searching on standard fields.
 * Do not provide full text search as ITQL do not support Regex search.
 *
 * Not a FS object as it doesn't inherit from fedora_fs_base.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_itql_query{

	public static function format_datetime($timestamp){
		if(empty($timestamp)){
			return $timestamp;
		}

		return '\''. date('Y-m-d', $timestamp) . 'T' . date('H:i:s', $timestamp). '.00Z\'^^<xsd:dateTime>';
	}

	public static function trim_namespaces($name){
		$result = $name;
		$result = str_replace('info:fedora/', '', $result);
		return $result;
	}

	public $start_date = null;
	public $end_date = null;
	public $owner = '';
	public $is_collection = false;

	public $hitPageSize = 0;
	public $offset = 0;
	public $sort = '';

	public function format(){
		$start_date = $this->start_date;
		$end_date = $this->end_date;
		$owner = $this->owner;
		$is_collection = $this->is_collection;

		$hitPageSize = $this->hitPageSize;
		$offset = $this->offset;
		$sort = $this->sort;

		$hitPageSize = $hitPageSize ? (int)$hitPageSize : 0;
		$hitPageSize = max($hitPageSize, 0);

		$offset = $offset ? (int)$offset : 0;
		$offset = max($offset, 0);

		$sort = $sort ? $sort : '$modified desc';

		$result = 'select $pid $modified $label $ownerId $created from <#ri> ';
		$result .= 'where( ';
		$result .= '$pid <fedora-model:hasModel> <info:fedora/fedora-system:FedoraObject-3.0> ';
		$result .= 'and $pid <fedora-view:lastModifiedDate> $modified ';
		$result .= 'and $pid <fedora-model:label> $label ';
		$result .= 'and $pid <fedora-model:ownerId> $ownerId ';
		$result .= 'and $pid <fedora-model:createdDate> $created ';
		if($start_date){
			$result .= 'and $modified <mulgara:after> ' . self::format_datetime($start_date) . ' in <#xsd> ' ;
		}
		if(is_numeric($end_date) && $end_date<time()){
			$result .= 'and $modified <mulgara:before> ' . self::format_datetime($end_date) . ' in <#xsd> ' ;
		}
		if($is_collection){
			$result .= 'and $pid <fedora-rels-ext:isCollection> \'true\' ';
		}
		if($owner){
			$result .= 'and $pid <fedora-model:ownerId> \''. $owner . '\' ';
		}
		if($is_collection){
			$result .= ') ';
		}else{
			$result .= ')minus(' ;
			$result .= '$pid <fedora-rels-ext:isCollection> \'true\' ';
			$result .= ') ';
		}
		if($sort){
			$result .= 'order by ' . $sort . ' ';
		}
		if($hitPageSize){
			$result .= 'limit '.$hitPageSize . ' ';
		}
		if($offset){
			$result .= 'offset '.$offset . ' ';
		}

		return $result;
	}

	public function query(FedoraProxy $fedora){
		$query = $this->format();
		$items = $fedora->ri_search($query, '', 'tuples', 'iTql', 'Sparql');

		foreach($items as &$item){
			$pid = self::trim_namespaces($item['pid']['@uri']);
			$item['pid'] = $pid;
		}
		return $items;
	}

	public function count(FedoraProxy $fedora){
		$query = $this->format();
		$result = $fedora->ri_search($query, '', 'tuples', 'iTql', 'count');
		return $result;
	}


}