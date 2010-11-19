<?php
namespace common\libraries;

/**
 * Helper class for generating the text representation of a SPARQL query based on keywork search.
 *
 * Not a FS object as it doesn't inherit from fedora_fs_base.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_sparql_query{

	public static function format_datetime($timestamp){
		if(empty($timestamp)){
			return $timestamp;
		}

		return '"'. date('Y-m-d', $timestamp) . 'T' . date('H:i:s', $timestamp). '.00Z"^^xsd:dateTime';
	}

	public static function trim_namespaces($name){
		$result = $name;
		$result = str_replace('info:fedora/', '', $result);
		return $result;
	}

	public $search = '';
	public $searchLevel = '';
	public $start_date = NULL;
	public $end_date = NULL;
	public $owner = '';
	public $hitPageSize = 0;
	public $offset = 0;
	public $sort = '';
	public $is_collection = false;

	public function format(){
		$search = $this->search;
		$searchLevel = $this->searchLevel;
		$start_date = $this->start_date;
		$end_date = $this->end_date;
		$owner = $this->owner;
		$hitPageSize = $this->hitPageSize;
		$offset = $this->offset;
		$sort = $this->sort;
		$is_collection = $this->is_collection;

		$search = trim($search);

		$hitPageSize = (int)$hitPageSize;
		$hitPageSize = $hitPageSize < 1 ? 0 : $hitPageSize;
		//$hitPageSize = empty($hitPageSize) ? self::get_max_results() : $hitPageSize;

		$offset = (int)$offset;
		$offset = $offset <= 0 ? 0 : $offset;

		$result[] = 'select ?pid ?label ?lastModifiedDate ?ownerId ?createdDate from <#ri>';
		$result[] = 'where{';
		$result[] = '?pid <fedora-model:hasModel> <info:fedora/fedora-system:FedoraObject-3.0>';
		$result[] = '.';
		$result[] = '?pid <fedora-model:createdDate> ?createdDate';
		$result[] = '.';
		$result[] = '?pid <fedora-view:lastModifiedDate> ?lastModifiedDate';
		if($start_date){
			$result[] = 'FILTER(?lastModifiedDate>='. self::format_datetime($start_date) .')';
		}
		if(is_numeric($end_date) && $end_date<time()){
			$result[] = 'FILTER(?lastModifiedDate<='. self::format_datetime($end_date) .')';
		}
		$result[] = '.';
		$result[] = '?pid <fedora-model:label> ?label';
		if(!empty($search)){
			if($searchLevel==1){
				$result[] = 'FILTER ?label ="'.$search.'")';
			}else if($searchLevel==2){
				$search = preg_quote($search);
				$search = str_replace('\\', '\\\\', $search);
				$pattern = ".*$search.*";
				$result[] = 'FILTER regex(?label , "'.$pattern.'", "i")';
			}else if($searchLevel==3){
				$result[] = 'FILTER regex(?label , "'.$search.'", "i")';
			}else{
				$search = preg_quote($search);
				$search = str_replace('\\', '\\\\', $search);
				$pattern = ".*$search.*";
				$result[] = 'FILTER regex(?label , "'.$pattern.'", "i")';
			}
		}
		$result[] = '.';
		$result[] = '?pid <fedora-model:ownerId> ?ownerId ';
		if(!empty($owner)){
			$result[] = '.';
			$result[] = " {?pid <fedora-model:ownerId> '".$owner."'}";
			$result[] = ' UNION';
			$result[] = ' { ?pid <http://purl.org/dc/terms/accessRights> \'public\' } ';
			$result[] = ' UNION ';
			$result[] = ' { ?pid <http://purl.org/dc/terms/accessRights> \'institution\' } ';
		}else{
			$result[] = '.';
			$result[] = ' { ?pid <http://purl.org/dc/terms/accessRights> \'public\' } ';
			$result[] = ' UNION ';
			$result[] = ' { ?pid <http://purl.org/dc/terms/accessRights> \'institution\' } ';
		}

		//leave optional constraint as last element to avoid issues.

		if($is_collection === NULL){
			;//no constraint returns both collection objects and non-collection objects
		}else if($is_collection){
			$result[] = '.';
			$result[] = '?pid <fedora-rels-ext:isCollection> ?col FILTER($col)';

		}else{
			$result[] = '.';
			$result[] = 'OPTIONAL {?pid <fedora-rels-ext:isCollection> ?col } FILTER( !BOUND(?col) || !?col)';
		}

		$result[] = '}';
		if(empty($sort)){
			$result[] = 'ORDER BY ASC(?lastModifiedDate)';
		}
		if($hitPageSize>0){
			$result[] = "LIMIT $hitPageSize";
		}
		if($offset>0){
			$result[] = "OFFSET $offset";
		}

		$result = implode(' ', $result);
		return $result;
	}

	public function query(FedoraProxy $fedora){
		$query = $this->format();
		$items = $fedora->ri_search($query, '', 'tuples', 'Sparql', 'Sparql');

		foreach($items as &$item){
			$pid = self::trim_namespaces($item['pid']['@uri']);
			$item['pid'] = $pid;
		}
		return $items;
	}

	public function count(FedoraProxy $fedora){
		$query = $this->format();
		$result = $fedora->ri_search($query, '', 'tuples', 'Sparql', 'count');
		return $result;
	}


}