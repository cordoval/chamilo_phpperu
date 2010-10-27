<?php
namespace common\libraries;

use DOMXPath;

require_once dirname(__FILE__) . '/rest/rest_proxy_base.class.php';

/**
 * A proxy to the fedora REST API. Transforms XML responses to arrays.
 * Provides partial implementation to the SWITCH api.
 *
 * @link https://wiki.duraspace.org/display/FCR30/REST+API#RESTAPI-ingest
 * @link http://www.switch.ch/collection/spec/
 * @version 3.3
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 * @author nicolas rod
 *
 */
class FedoraProxy extends RestProxyBase{

	const SWITCH_DISCLIPLINES_URL = 'https://collection.switch.ch/spec/2008/disciplines/disciplines.csv';
	const SWITCH_LICENSES_URL = 'https://collection.switch.ch/spec/2008/licenses/licenses.csv';


	const DUBLIN_CORE_DS_NAME          	= 'DC';
	const LOM_DS_NAME					= 'LOM';
	const OBJECT_DS_NAME              	= 'OBJECT';

	const FEDORA_NAMESPACE = 'http://www.fedora.info/definitions/1/0/types/';
	const FEDORA_SYSTEM = 'fedora-system';

	const RI_TUPLES = 'tuples';
	const RI_TRIPLES = 'triples';

	/**
	 * URL Syntax
	 * 	/objects ? [terms | query] [maxResults] [resultFormat] [pid] [label] [state] [ownerId] [cDate] [mDate] [dcmDate] [title] [creator] [subject] [description] [publisher] [contributor] [date] [type] [format] [identifier] [source] [language] [relation] [coverage] [rights]
	 * HTTP Method
	 * 	GET
	 * HTTP Response
	 * 200
	 * Parameters
	 * Name  Description  Default  Options
	 * terms  a phrase represented as a sequence of characters (including the ? and * wildcards) for the search. If this sequence is found in any of the fields for an object, the object is considered a match. Do NOT use this parameter in combination with the "query" parameter
	 * query  a sequence of space-separated conditions. A condition consists of a metadata element name followed directly by an operator, followed directly by a value. Valid element names are (pid, label, state, ownerId, cDate, mDate, dcmDate, title, creator, subject, description, publisher, contributor, date, type, format, identifier, source, language, relation, coverage, rights). Valid operators are: contains (), equals (=), greater than (>), less than (<), greater than or equals (>=), less than or equals (<=). The contains () operator may be used in combination with the ? and * wildcards to query for simple string patterns. Space-separators should be encoded in the URL as %20. Operators must be encoded when used in the URL syntax as follows: the (=) operator must be encoded as %3D, the (>) operator as %3E, the (<) operator as %3C, the (>=) operator as %3E%3D, the (<=) operator as %3C%3D, and the (~) operator as %7E. Values may be any string. If the string contains a space, the value should begin and end with a single quote character ('). If all conditions are met for an object, the object is considered a match. Do NOT use this parameter in combination with the "terms" parameter
	 * maxResults  the maximum number of results that the server should provide at once. If this is unspecified, the server will default to a small value  25
	 * resultFormat  the preferred output format  html  xml, html
	 * pid if true, the Fedora persistent identifier (PID) element of matching objects will be included in the response  false  true, false
	 * label  if true, the Fedora object label element of matching objects will be included in the response  false  true, false
	 * state  if true, the Fedora object state element of matching objects will be included in the response  false  true, false
	 * ownerId  if true, each matching objects' owner id will be included in the responsefalsetrue, false  false  true, false
	 * cDate  if true, the Fedora create date element of matching objects will be included in the response  false  true, false
	 * mDate  if true, the Fedora modified date of matching objects will be included in the response  false  true, false
	 * dcmDate  if true, the Dublin Core modified date element(s) of matching objects will be included in the response  false  true, false
	 * title  if true, the Dublin Core title element(s) of matching objects will be included in the response  false  true, false
	 * creator  if true, the Dublin Core creator element(s) of matching objects will be included in the response  false  true, false
	 * subject  if true, the Dublin Core subject element(s) of matching objects will be included in the response  false  true, false
	 * description  if true, the Dublin Core description element(s) of matching objects will be included in the response  false  true, false
	 * publisher  if true, the Dublin Core publisher element(s) of matching objects will be included in the response  false  true, false
	 * contributor  if true, the Dublin Core contributor element(s) of matching objects will be included in the response  false  true, false
	 * date  if true, the Dublin Core date element(s) of matching objects will be included in the response  false  true, false
	 * type  if true, the Dublin Core type element(s) of matching objects will be included in the response  false  true, false
	 * format  if true, the Dublin Core format element(s) of matching objects will be included in the response  false  true, false
	 * identifier  if true, the Dublin Core identifier element(s) of matching objects will be included in the response  false  true, false
	 * source  if true, the Dublin Core source element(s) of matching objects will be included in the response  false  true, false
	 * language  if true, the Dublin Core language element(s) of matching objects will be included in the response  false  true, false
	 * relation  if true, the Dublin Core relation element(s) of matching objects will be included in the response  false  true, false
	 * coverage  if true, the Dublin Core coverage element(s) of matching objects will be included in the response  false  true, false
	 * rights  if true, the Dublin Core rights element(s) of matching objects will be included in the response  false  true, false
	 *
	 * Examples
	 * 		/objects?terms=demo&pid=true&subject=true&label=true&resultFormat=xml
	 * 		/objects?query=title%7Erome%20creator%7Estaples&pid=true&title=true&creator=true
	 * 		/objects?query=pid%7E*1&maxResults=50&format=xml&pid=true&title=true
	 */
	public function find_objects($parameters = array()){
		return $this->execute('objects', $parameters, 'get');
	}

	public function list_user_objects($user_id){
		$query = "ownerId=$user_id";
		return $this->list_objects($query);
	}

	public function list_objects($query='', $args = array()){
		$args['resultFormat'] = 'xml';
		$args['pid'] = true;
		$args['label'] = true;
		$args['ownerId'] = true;
		$args['cDate'] = true;
		$args['mDate'] = true;
		$args['dcmDate'] = true;
		$args['title'] = true;
		$args['description'] = true;
		$args['pid'] = true;
		$args['identifier'] = true;

		//$query = urlencode($query);
		$namespace = $this->get_config()->get_return_pid_namespace();
		if(!empty($namespace) && strpos($query, 'pid') === false){
			$filter = "pid~'$namespace:*'";
			$query = empty($query) ? $filter : "$query%20$filter";
		}
		$args['query'] = $query;//even if query is empty it must be output on the url

		$max_results = $this->get_config()->get_max_results();
		if(!empty($max_results)){
			$args['maxResults'] = $max_results;
		}
		$state = $this->get_config()->get_return_state();
		if(!empty($state)){
			$args['state'] = $state;
		}

		$response_document = $this->find_objects($args);

		$result = array();
		if(isset($response_document)){
			$xpath = new DOMXPath($response_document);
			$xpath->registerNamespace('f', self::FEDORA_NAMESPACE);

			$node_list = $xpath->query('/f:result/f:resultList/f:objectFields');
			if($node_list->length > 0 ){
				foreach($node_list as $object_node){
					if($this->accept_object($object_node)){
						$object = $this->xml_to_array($response_document, $object_node);
						$object['cdate']     = self::parse_date($object['cdate']);
						$object['mdate']     = self::parse_date($object['mdate']);
						$object['dcmdate']   = self::parse_date($object['dcmdate']);
						$result[] = $object;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Search using SWITCH collection REST call.
	 *
	 * https://collection.switch.ch/LOREST/objects/find?query=searchTerm&searchLevel=1&hitPageSize=hitPageSize&hitPageStart=hitPageStart&sortFields=sortOrder
	 *
	 * query (requried): String. The word or phrase you are looking for. See Lucene Site for more information. http://lucene.apache.org/java/2_4_1/queryparsersyntax.html
	 *
	 * searchLevel (optional): Integer.
	 * 		1 = exact search for the searchTerm.
	 * 		2 = adds a wildcard (*) at the end of the searchTerm.
	 * 		3 = adds a Tilde(~) for Fuzzy search at the end of the searchTerm.
	 * If no searchLevel is given, Collection search best effort, which meens it tries the next higher searchLevel if no result is found on the lower.
	 *
	 * hitPageSize (optional): How many results should be returned per Page. Default is 1000
	 * hitPageStart (optional): At wich result the results shhould start displaing.
	 * sortFields (optional): Sort order.
	 * 		Default ist by relevance (PID,SCORE,false).
	 * 		Title (fgs.label,STRING,false;PID,SCORE)
	 * 		Date (fgs.lastModifiedDate,AUTO,true;PID,SCORE)
	 * 		Author (chor_dcterms.creatorSorted,STRING,false;PID,SCORE).
	 * To change the sort direction, toggle the false to true and vice versa.
	 * @return array of hits
	 */
	public function SWITCH_find($args){
		$response_document = $this->execute('objects/find', $args, 'get');
		$result = array();
		if(isset($response_document)){
			$xpath = new DOMXPath($response_document);

			$hits = $xpath->query('//hit');
			if($hits->length > 0 ){
				foreach($hits as $hit){
					$object = array();
					$fields = $xpath->query('./field', $hit);
					if($fields->length > 0){
						foreach($fields as $field){
							//$parts = explod('.', $field->getAttribute('name'));
							$field_name = $field->attributes->getNamedItem('name')->value;
							$value = $field->nodeValue;
							$parts = explode('.', $field_name);
							$f = count($parts)==2 ? $parts[1] : $parts[0];
							$f = strtolower($f);
							if($f == 'created' || $f == 'createddate' || $f == 'lastmodifieddate'){
								$value = self::parse_date($value);
							}
							if(isset($object[$field_name])){
								$object[$field_name] = array_merge((array)$object[$field_name], (array)$value);
							}else{
								$object[$field_name] = $value;
							}
						}
					}
					if(!empty($object)){
						$result[] = $object;
					}
				}
			}
		}
		return $result;
	}

	public function SWITCH_collections($collection_id = ''){
		$url = empty($collection_id) ? 'collections' : "collections/$collection_id";
		$response_document = $this->execute($url, array(), 'get');
		$result = array();
		if(isset($response_document)){
			$xpath = new DOMXPath($response_document);

			$hits = $xpath->query('//item');
			if($hits->length > 0 ){
				foreach($hits as $hit){
					$object = array();
					$fields = $xpath->query('./*', $hit);
					if($fields->length > 0){
						foreach($fields as $field){
							$field_name = $field->nodeName;
							if(!empty($field_name)){
								$value = $field->nodeValue;
								$f = strtolower($field_name);
								if($f == 'created' || $f == 'createddate' || $f == 'lastmodifieddate'){
									$value = self::parse_date($value);
								}
								if(isset($object[$field_name])){
									$object[$field_name] = array_merge((array)$object[$field_name], (array)$value);
								}else{
									$object[$field_name] = $value;
								}
							}
						}
					}
					if(!empty($object)){
						$result[] = $object;
					}
				}
			}
		}
		return $result;
	}

	public function SWITCH_collections_objects($collection_id){
		$url = empty($collection_id) ? 'collections' : "collections/$collection_id/objects";
		$response_document = $this->execute($url, array(), 'get');
		$result = array();
		if(isset($response_document)){
			$xpath = new DOMXPath($response_document);

			$hits = $xpath->query('//item');
			if($hits->length > 0 ){
				foreach($hits as $hit){
					$object = array();
					$fields = $xpath->query('./*', $hit);
					if($fields->length > 0){
						foreach($fields as $field){
							$field_name = $field->nodeName;
							if(!empty($field_name)){
								$value = $field->nodeValue;
								$f = strtolower($field_name);
								if($f == 'created' || $f == 'createddate' || $f == 'lastmodifieddate'){
									$value = self::parse_date($value);
								}
								if(isset($object[$field_name])){
									$object[$field_name] = array_merge((array)$object[$field_name], (array)$value);
								}else{
									$object[$field_name] = $value;
								}
							}
						}
					}
					if(!empty($object)){
						$result[] = $object;
					}
				}
			}
		}
		return $result;
	}

	public function SWITCH_find_discipline($discipline){
		$args = array();
		$args['query'] = "discipline:$discipline";
		return $this->SWITCH_find($args);
	}

	/**
	 * Fetch an array of discliplines from SWITCH.
	 * If an id is provided returns either the discipline if found or an empty array.
	 * If no id is provided returns all discliplines.
	 *
	 * @link https://collection.switch.ch/spec/2008/disciplines/disciplines.csv
	 * @param string $id
	 * @return array
	 */
	public function SWITCH_get_disciplines($id=false){
		static $disciplines = false;
		if($disciplines === false){

			$bom = pack("CCC",0xef,0xbb,0xbf); //UTF8 Byte Order Mark
			$content = file_get_contents(dirname(__FILE__) . '/resource/switch/disciplines.csv');
			$content = substr($content, 0,3) == $bom ? substr($content, 3) : $content;


			/*
			 * call SWITCH web site to get the list of disciplines
			 *
			 $disciplines = array();
			 $options = array(
			 CURLOPT_RETURNTRANSFER => true,     // return web page
			 CURLOPT_HEADER         => false,    // don't return headers
			 CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			 CURLOPT_ENCODING       => '',  		// handle all encodings
			 CURLOPT_USERAGENT      => 'spider', // who am i
			 CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			 CURLOPT_CONNECTTIMEOUT => 5,      // timeout on connect
			 CURLOPT_TIMEOUT        => 5,      // timeout on response
			 CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			 CURLOPT_SSL_VERIFYPEER => false,
			 CURLOPT_SSL_VERIFYHOST => false,
			 );
			 $ch = curl_init(self::SWITCH_DISCLIPLINES_URL);
			 curl_setopt_array($ch, $options);
			 $content = curl_exec($ch);
			 $content = utf8_encode($content);
			 $err  = curl_errno($ch);
			 curl_close($ch);
			 if($err){
				return $disciplines = array();
				}

				//$err  = curl_errno($ch);
				//$errmsg = curl_error($ch);
				//$header = curl_getinfo($ch);
				*/

			if($content){
				$lines = explode("\n", $content);
				foreach($lines as $line){
					if($line = trim($line)){
						$fields = explode(',', $line);
						$record = array();

						//remove upper byte encoding
						$field_id = (int)$fields[0];
						$field_id = $field_id ? $field_id : '';

						$record['id'] = $field_id;
						$record['parent'] = $fields[1];
						$record['order'] = $fields[2];
						$record['level'] = $fields[3];
						$record['german'] = $fields[4];
						$record['english'] = $fields[5];
						$record['french'] = $fields[6];
						$record['italian'] = $fields[7];
						$record['shis_id'] = $fields[8];
						$disciplines[$field_id] = $record;
					}
				}
			}
		}
		if($id){
			$result = isset($disciplines[$id]) ? $disciplines[$id] : array();
		}else{
			$result = $disciplines;
		}
		return $result;
	}

	/**
	 * Fetch an array of licenses from SWITCH.
	 * If an id is provided returns either the license if found or an empty array.
	 * If no id is provided returns all licenses.
	 *
	 * @link https://collection.switch.ch/spec/2008/licenses/licenses.csv
	 * @param string $id
	 * @return array
	 */
	public function SWITCH_get_licenses($id=false){
		static $licenses = false;
		if($licenses === false){

			$bom = pack("CCC",0xef,0xbb,0xbf); //UTF8 Byte Order Mark
			$content = file_get_contents(dirname(__FILE__) . '/resource/switch/licenses.csv');
			$content = substr($content, 0,3) == $bom ? substr($content, 3) : $content;

			/*
			 * call SWITCH web site to get the list of licenses
			 *
			 $licenses = array();
			 $options = array(
			 CURLOPT_RETURNTRANSFER => true,     // return web page
			 CURLOPT_HEADER         => false,    // don't return headers
			 CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			 CURLOPT_ENCODING       => '',  		// handle all encodings
			 CURLOPT_USERAGENT      => 'spider', // who am i
			 CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			 CURLOPT_CONNECTTIMEOUT => 5,      // timeout on connect
			 CURLOPT_TIMEOUT        => 5,      // timeout on response
			 CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			 CURLOPT_SSL_VERIFYPEER => false,
			 CURLOPT_SSL_VERIFYHOST => false,
			 );
			 $ch = curl_init(self::SWITCH_LICENSES_URL);
			 curl_setopt_array($ch, $options);
			 $content = curl_exec($ch);
			 $err  = curl_errno($ch);
			 curl_close($ch);
			 if($err){
				return $licenses = array();
				}

				//$err  = curl_errno($ch);
				//$errmsg = curl_error($ch);
				//$header = curl_getinfo($ch);

				*/

			if($content){
				$lines = explode("\n", $content);
				foreach($lines as $line){
					if(trim($line)){
						$fields = explode(',', $line);
						$record = array();
						$record['id'] = $field_id = $fields[0];
						$record['german'] = $fields[1];
						$record['english'] = $fields[2];
						$record['french'] = $fields[3];
						$record['italian'] = $fields[4];
						$licenses[$field_id] = $record;
					}
				}
			}
		}
		if($id){
			$result = isset($licenses[$id]) ? $licenses[$id] : array();
		}else{
			$result = $licenses;
		}
		return $result;
	}

	public function SWITCH_list_datastreams($pid){
		$args = array();
		$args['format'] = 'xml';
		$response_document = $this->execute("objects/$pid/datastreams", $args, 'get');

		$result = array();
		if(isset($response_document)){
			$xpath = new DOMXPath($response_document);
			$node_list = $xpath->query('//item');
			if($node_list->length > 0 ){
				foreach($node_list as $node){
					if($node instanceof DOMElement){
						$ds = $this->xml_to_array($response_document, $node);
						$result[] = $ds;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * The RISearch service can be programmatically accessed via HTTP GET or POST.
	 * To avoid character encoding issues, POST should always be used when the query is passed in by value and contains non-ASCII characters.
	 * As with the user interface, it can be invoked to retrieve tuples or triples. The syntax is described below.
	 *
	 * Note:
	 * 		â€¢Square brackets ( "[" and "]" ) indicate that the parameter is optional.
	 * 		â€¢As with all HTTP parameters, unsafe URI characters should be URI-escaped. For readability purposes, URI escaping is not shown below.
	 * 		â€¢The query and template parameters optionally take the value by reference â€“ that is, a URL to a query or template can be given instead of the actual text.
	 * 		â€¢The flush parameter tells the resource index to ensure that any recently-added/modified/deleted triples are flushed to the triplestore before executing the query. This option can be desirable in certain scenarios, but for performance reasons, should be used sparingly when a process is making many API-M calls to Fedora in a short period of time: We have found that Mulgara generally achieves a much better overall update rate with large batches of triples.
	 *
	 * @param $query
	 * @param $template
	 * @param $type
	 * @param $lang
	 * @param $format
	 * @param $limit
	 * @param $distinct
	 * @param $stream
	 * @param $flush
	 */
	public function ri_search($query, $template = '', $type = 'tuples', $lang='Sparql', $format = 'Sparql', $limit = '', $distinct = false, $stream = false, $flush = false){
		$args = array();
		$args['type'] = $type;
		$args['flush'] = $flush ? 'true' : 'false';
		$args['lang'] = $lang;
		$args['format'] = $format;
		if(!empty($limit)){
			$args['limit'] = $limit;
		}
		$args['distinct'] = $distinct ? 'on' : 'off';
		$args['stream'] = $stream ? 'on' : 'off';
		$args['dt'] = 'on';
		$args['query'] = $query;
		if(!empty($template)){
			$args['template'] = $template;
		}
		$post = implode("\n", $args);
		if(strtolower($format)=='sparql'){
			$response_document = $this->execute('risearch', $args, 'get');

			$result = array();
			if(isset($response_document)){
				$xpath = new DOMXPath($response_document);
				$xpath->registerNamespace('r', 'http://www.w3.org/2001/sw/DataAccess/rf1/result');
				$node_list = $xpath->query('//r:result');
				if($node_list->length > 0 ){
					foreach($node_list as $node){
						if($node instanceof DOMElement){
							$ds = $this->xml_to_array($response_document, $node);
							$result[] = $ds;
						}
					}
				}
			}
		}else if(strtolower($format)=='count'){
			$result = $this->execute_raw('risearch', $args, 'get');
			return $result;

		}else{
			$result =  $this->execute_raw('risearch', $args, 'get');
			//$result = $this->execute_raw('risearch', array(), 'post', array('content'=>$args));
		}
		return $result;
	}

	/**
	 * Search for an object with a label=$label, owner=$owner and object belonging to $collection.
	 *
	 * @param string $label the object's label to search for
	 * @param string $owner if provided search only for objects belonging to this owner
	 * @param string $collection  if provided search only for objects belonging to this collection @todo not yet implemented
	 * @return false|array If found returns the object's id. If not found returns false.
	 */
	public function get_object_by_label($label, $owner='', $collection=''){
		$label = preg_quote($label);
		$label = str_replace('\\', '\\\\', $label);
		$query = 'select ?pid ?label ?lastModifiedDate ?ownerId from <#ri> where{'; '';
		$query .= '?pid <fedora-model:hasModel> <info:fedora/fedora-system:FedoraObject-3.0> . ';
		$query .= '?pid <fedora-view:lastModifiedDate> ?lastModifiedDate . ';
		$query .= '?pid <fedora-model:label> ?label FILTER regex(?label , "^'.$label.'$", "i") . ';
		$query .= '?pid <fedora-model:ownerId> ?ownerId  . ';
		if($owner){
			$query .= "?pid <fedora-model:ownerId> '$owner' . " ;
		}
		$query .= 'OPTIONAL {?pid <fedora-rels-ext:isCollection> ?col} FILTER( !BOUND(?col) || !?col) ';
		$query .= '} ORDER BY DESC(?lastModifiedDate) LIMIT 1 ';

		$items = $this->ri_search($query, '', 'tuples', 'Sparql', 'Sparql');

		$result = false;
		foreach($items as &$item){
			$pid = str_replace('info:fedora/', '', $item['pid']['@uri']);
			$item['pid'] = $pid;
			if($result == false){
				$result = $item;
			}else{
				if($result['lastmodifieddate']<$item['lastmodifieddate']){
					$result = $item;
				}
			}
		}
		$result = $result ? $result : false;
		return $result;
	}

	/**
	 * getNextPID
	 *
	 * URL Syntax
	 * 		/objects/nextPID ? [numPIDs] [namespace] [format]
	 * HTTP Method
	 * 		POST
	 * HTTP Response
	 * 		200
	 * Parameters
	 * 		Name  Description  Default  Options
	 * 		numPIDs  the number of pids to retrieve  1
	 * 		namespace  the namespace of the requested pid(s)  the default namespace of the repository
	 * 		format  the preferred output format  html  xml, html
	 *
	 * Examples
	 * 		POST: /objects/nextPID
	 * 		POST: /objects/nextPID?numPIDs=5&namespace=test&format=xml
	 *
	 * @param $numPIDs
	 * @param $namespace
	 * @param $format
	 */
	public function get_nextPID($numPIDs=1, $namespace = '', $format = 'xml'){
		$args = array();
		if(!empty($numPIDs)){
			$args['numPIDs'] = $numPIDs;
		}
		if(!empty($namespace)){
			$args['namespace'] = $namespace;
		}
		if(!empty($format)){
			$args['format'] = $format;
		}
		$doc = $this->execute('objects/nextPID', $args, 'post');

		$result = array();
		if(isset($doc)){
			$xpath = new DOMXPath($doc);
			$node_list = $xpath->query('//pid');
			if($node_list->length > 0 ){
				foreach($node_list as $node){
					if($node instanceof DOMElement){
						$result[] = $node->nodeValue;
					}
				}
			}
		}
		if(empty($numPIDs) || $numPIDs ==1){
			$result = reset($result);
		}
		return $result;
	}

	/**
	 * URL Syntax
	 * 		/objects/{pid}/datastreams ? [format] [asOfDateTime]
	 * HTTP Method
	 * 		GET
	 * HTTP Response
	 * 		200
	 * Parameters
	 * 		Name  Description  Default  Options
	 * 		{pid}  persistent identifier of the digital object
	 * 		format  the preferred output format  html  xml, html
	 * 		asOfDateTime  indicates that the result should be relative to the digital object as it existed on the given date    yyyy-MM-dd or yyyy-MM-ddTHH:mm:ssZ
	 *
	 * Examples
	 *
	 * /objects/demo:35/datastreams
	 * /objects/demo:35/datastreams?format=xml&asOfDateTime=2008-01-01T05:15:00Z
	 * @param string $pid
	 */
	public function list_datastreams($pid){
		$args = array();
		$args['format'] = 'xml';
		$response_document = $this->execute("objects/$pid/datastreams", $args, 'get');

		$result = array();
		if(isset($response_document)){
			$xpath = new DOMXPath($response_document);
			$node_list = $xpath->query('//datastream');
			if($node_list->length > 0 ){
				foreach($node_list as $node){
					if($node instanceof DOMElement){
						$attributes = $node->attributes;
						$ds = array();
						foreach($attributes as $a){
							$ds[$a->nodeName] = $a->nodeValue;
						}
						if(!empty($ds)){
							$result[] = $ds;
						}
					}
				}
			}
		}
		return $result;
	}

	public function get_datastream($pid, $dsID){
		$args = array();
		$args['format'] = 'xml';
		$response_document = $this->execute("objects/$pid/datastreams/$dsID", $args, 'get');

		$result = array();
		if(isset($response_document)){
			//$xpath = new DOMXPath($response_document);
			$ds = $this->xml_to_array($response_document, $response_document);
			$result = isset($ds['result']) ? $ds['result'] : $ds;
		}
		return $result;
	}

	/**
	 * getObjectXML
	 * URL Syntax
	 * 		/objects/{pid}/objectXML
	 * HTTP Method
	 * 		GET
	 * HTTP Response
	 * 		200
	 * Parameters
	 *
	 * 		Name  Description  Default  Options
	 * 		{pid}  persistent identifier of the digital object
	 *
	 * Examples
	 *
	 * /objects/demo:29/objectXML
	 * @param $pid
	 */
	public function get_object_xml($pid){
		return $this->execute("objects/$pid/objectXML", array(), 'get');
	}

	public function get_object($pid, $parameters = array()){
		if(empty($parameters)){
			$parameters['format'] = 'xml';
		}
		$response_document = $this->execute("objects/$pid", $parameters, 'get');
		$result = false;
		if(isset($response_document)){
			$xpath = new DOMXPath($response_document);

			$hits = $xpath->query('/result');
			if($hits->length > 0 ){
				foreach($hits as $hit){
					$object = $this->xml_to_array($response_document, $hit);
					$result = empty($object) ? false : $object; //only one hit is expected
				}
			}
		}
		return $result;
	}

	/**
	 * getObjectProfil
	 * URL Syntax
	 * 		/objects/{pid} ? [format] [asOfDateTime]
	 * HTTP Method
	 * 		GET
	 * HTTP Response
	 * 		200
	 * Parameters
	 * 		Name  Description  Default  Options
	 * 		{pid}  persistent identifier of the digital object
	 * 		format  the preferred output format  html  xml, html
	 * 		asOfDateTime  indicates that the result should be relative to the digital object as it existed on the given date    yyyy-MM-dd or yyyy-MM-ddTHH:mm:ssZ
	 *
	 * Examples
	 * 		/objects/demo:29
	 * 		/objects/demo:29?format=xml
	 * 		/objects/demo:29?asOfDateTime=2008-01-01
	 *
	 * @param string $pid
	 */
	public function get_object_profile($pid, $args = array()){
		$args['format'] = 'xml';
		$response_document = $result =  $this->execute("objects/$pid", $args, 'get');
		$result = false;
		if(isset($response_document)){
			$result = $this->xml_to_array($response_document, $response_document);
			$result = $result ? reset($result) : false;
		}
		return $result;
	}

	/**
	 * getDatastream
	 * URL Syntax
	 * 		/objects/{pid}/datastreams/{dsID} ? [asOfDateTime] [format] [validateChecksum]
	 * HTTP Method
	 * 		GET
	 * HTTP Response
	 * 		200
	 * Parameters
	 * 		Name  Description  Default  Options
	 * 		{pid}  persistent identifier of the digital object
	 * 		{dsID}  datastream identifier
	 * 		format  the preferred output format  html  xml, html
	 * 		asOfDateTime  indicates that the result should be relative to the digital object as it existed on the given date    yyyy-MM-dd or yyyy-MM-ddTHH:mm:ssZ
	 * 		validateChecksum  verifies that the Datastream content has not changed since the checksum was initially computed. If asOfDateTime is null, Fedora will use the most recent version.  false  true, false
	 * Examples
	 * 		/objects/demo:29/datastreams/DC
	 * 		/objects/demo:29/datastreams/DC?format=xml
	 * 		/objects/demo:29/datastreams/DC?format=xml&validateChecksum=true
	 */
	public function get_datastream_content($pid, $dsID){
		$args = array();
		$result =  $this->execute_raw("objects/$pid/datastreams/$dsID/content", $args, 'get');
		return $result;
	}

	public function get_datastream_content_url($pid, $dsID){
		$base = $this->get_config()->get_base_url();
		$result = "$base/objects/$pid/datastreams/$dsID/content";
		return $result;
	}

	/**
	 * ingest
	 *
	 * URL Syntax
	 * 		/objects/ [{pid}| new] ? [label] [format] [encoding] [namespace] [ownerId] [logMessage] [ignoreMime]
	 * HTTP Method
	 * 		POST
	 * HTTP Response
	 * 		201
	 * Request Content
	 * 		text/xml
	 * Parameters
	 * 		Name  Description  Default  Options
	 * 		{pid}  persistent identifier of the object to be created
	 * 		new  indicator that either a new PID should be created for this object or that the PID to be used is encoded in the XML included as the body of the request
	 * 		label  the label of the new object
	 * 		format  the XML format of the object to be ingested  info:fedora/fedora-system:FOXML-1.1, info:fedora/fedora-system:FOXML-1.0, info:fedora/fedora-system:METSFedoraExt-1.1, info:fedora/fedora-system:METSFedoraExt-1.0, info:fedora/fedora-system:ATOM-1.1, info:fedora/fedora-system:ATOMZip-1.1
	 * 		encoding  the encoding of the XML to be ingested  UTF-8
	 * 		namespace  the namespace to be used to create a PID for a new empty object; if object XML is included with the request, the namespace parameter is ignored  the default namespace of the repository
	 * 		ownerId  the id of the user to be listed at the object owner
	 * 		logMessage  a message describing the activity being performed
	 * 		ignoreMime  indicates that the request should not be checked to ensure that the content is XML prior to attempting an ingest. This is provided to allow for client applications which do not indicate the correct Content-Type when submitting a request.  false  true, false
	 * 		XML file as request content  file to be ingested as a new object
	 * Notes
	 * 		Executing this request with no request content will result in the creation of a new, empty object (with either the specified PID or a system-assigned PID). The new object will contain only a minimal DC datastream specifying the dc:identifier of the object.
	 * Examples
	 * 		POST: /objects/new
	 * 		POST: /objects/new?namespace=demo
	 * 		POST: /objects/test:100?label=Test
	 */
	public function ingest($xml_content, $pid=0, $label='', $owner_id='', $format='info:fedora/fedora-system:FOXML-1.1', $encoding='UTF-8', $namespace='', $logMessage='', $ignoreMime = false){
		$args = array();
		if(!empty($label)){
			$args['label'] = $label;
		}
		if(!empty($owner_id)){
			$args['owner_id'] = $owner_id;
		}
		if(!empty($format)){
			$args['format'] = $format;
		}
		if(!empty($encoding)){
			$args['encoding'] = $encoding;
		}
		if(!empty($namespace)){
			$args['namespace'] = $namespace;
		}
		if(!empty($logMessage)){
			$args['logMessage'] = $logMessage;
		}
		if(!empty($ignoreMime)){
			$args['ignoreMime'] = $ignoreMime;
		}

		$pid = empty($pid) ? 'new' : $pid;
		$result = $this->execute_raw("objects/$pid", $args, 'POST', $xml_content, 'text/xml');
		return $result;
	}

	/**
	 * purgeObject
	 *
	 * URL Syntax
	 * 		/objects/{pid} ? [logMessage]
	 * HTTP Method
	 * 		DELETE
	 * HTTP Response
	 * 		204
	 * Parameters
	 * 		{pid}  persistent identifier of the digital object
	 * 		logMessage  a message describing the activity being performed
	 * Examples
	 * 		DELETE: /objects/demo:29
	 *
	 * @param $pid
	 * @param $logMessage
	 */
	public function purge_object($pid, $logMessage=''){
		$args = array();
		if($logMessage){
			$args['logMessage'] = $logMessage;
		}

		$result = $this->execute_raw("objects/$pid", $args, 'DELETE');
		return $result;
	}

	/**
	 * Utility function. If a datastream exists modify it. Otherwise add it.
	 *
	 * @param $pid
	 * @param $dsID
	 * @param $dsLabel
	 * @param $content
	 * @param $mimeType
	 * @param $versionable
	 * @param $dsState
	 * @param $controlGroup
	 * @param $dsLocation
	 * @param $altIDs
	 * @param $formatURI
	 * @param $checksum
	 * @param $logMessage
	 * @param $ignoreContent
	 * @param $lastModifiedDate
	 */
	public function update_datastream($pid, $dsID, $dsLabel, $content=false, $mimeType = false, $versionable=true, $dsState='A', $controlGroup = 'M', $dsLocation=false, $altIDs = false, $formatURI = false, $checksum = false, $logMessage = false, $ignoreContent = false, $lastModifiedDate = false){
		try{
			return $this->modify_datastream($pid, $dsID, $dsLabel, $content, $mimeType, $versionable, $dsState, $dsLocation, $altIDs, $formatURI, $checksum, $logMessage, $ignoreContent, $lastModifiedDate);
		}catch(Exception $e){
			return $this->add_datastream($pid, $dsID, $dsLabel, $content, $mimeType, $versionable, $dsState, $controlGroup, $dsLocation, $altIDs, $formatURI, $checksum, $logMessage, $ignoreContent, $lastModifiedDate);
		}
	}

	/**
	 * addDatastream
	 *
	 * URL Syntax
	 * 		/objects/{pid}/datastreams/{dsID} ? [controlGroup] [dsLocation] [altIDs] [dsLabel] [versionable] [dsState] [formatURI] [checksumType] [checksum] [mimeType] [logMessage]
	 * HTTP Method
	 * 		POST
	 * HTTP Response
	 * 		201
	 * Parameters
	 * 		Name  Description  Default  Options
	 * 		{pid}  persistent identifier of the digital object
	 * 		{dsID}  datastream identifier
	 * 		controlGroup  one of "X", "M", "R", or "E" (Inline *X*ML, *M*anaged Content, *R*edirect, or *E*xternal Referenced)  X  X, M, R, E
	 * 		dsLocation  location of managed or external datastream content
	 * 		altIDs  alternate identifiers for the datastream
	 * 		dsLabel  the label for the datastream
	 * 		versionable  enable versioning of the datastream  true  true, false
	 * 		dsState  one of "A", "I", "D" (*A*ctive, *I*nactive, *D*eleted)  A  A, I, D
	 * 		formatURI  the format URI of the datastream
	 * 		checksumType  the algorithm used to compute the checksum  DEFAULT  DEFAULT, DISABLED, MD5, SHA-1, SHA-256, SHA-385, SHA-512
	 * 		checksum  the value of the checksum represented as a hexadecimal string
	 * 		mimeType  the MIME type of the content being added, this overrides the Content-Type request header
	 * 		logMessage  a message describing the activity being performed
	 * 		multipart file as request content  datastream file (for Managed datastreams)
	 * Examples
	 * 		POST: /objects/demo:29/datastreams/NEWDS?controlGroup=X&dsLabel=New (with Multipart file)
	 * 		POST: /objects/demo:29/datastreams/NEWDS?controlGroup=M&dsLocation=http://example:80/newds&dsLabel=New
	 *
	 *
	 */
	public function add_datastream($pid, $dsID, $dsLabel, $content=false, $mimeType = false, $versionable=true, $dsState='A', $controlGroup = 'M', $dsLocation=false, $altIDs = false, $formatURI = false, $checksum = false, $logMessage = false, $ignoreContent = false, $lastModifiedDate = false){
		$args = array();

		if($dsLocation !== false){
			$args['dsLocation'] = $dsLocation;
		}
		if($dsLabel !== false){
			$args['dsLabel'] = $dsLabel;
		}
		if($altIDs !== false){
			$args['altIDs'] = $altIDs;
		}
		$args['versionable'] = $versionable;
		if($dsState !== false){
			$args['dsState'] = $dsState;
		}
		if($formatURI !== false){
			$args['formatURI'] = $formatURI;
		}
		if($checksum !== false){
			$args['checksum'] = $checksum;
		}
		if($logMessage !== false){
			$args['logMessage'] = $logMessage;
		}
		if($ignoreContent !== false){
			$args['ignoreContent'] = $ignoreContent;
		}
		if($lastModifiedDate !== false){
			$args['lastModifiedDate'] = $lastModifiedDate;
		}
		if($controlGroup){
			$args['controlGroup'] = $controlGroup;
		}
		$data_to_send = $content ? $content : null;
		$mimeType = $mimeType ? $mimeType : '';
		$result = $this->execute_raw("objects/$pid/datastreams/$dsID", $args, 'POST', $data_to_send, $mimeType);
		return $result;
	}

	/**
	 * modifyObject
	 *
	 * URL Syntax
	 * 		/objects/{pid} ? [label] [ownerId] [state] [logMessage] [\lastModifiedDate]
	 *
	 * HTTP Method
	 * 		PUT
	 *
	 * HTTP Response
	 * 		200
	 *
	 * Parameters
	 * 		Name  Description  Default  Options
	 * 		{pid}  persistent identifier of the digital object
	 * 		label  the new object label
	 * 		ownerId  the id of the user to be listed at the object owner
	 * 		state  the new object state - *A*ctive, *I*nactive, or *D*eleted  A  A, I, D
	 * 		logMessage  a message describing the activity being performed
	 * 		lastModifiedDate  date/time of the last (known) modification to the datastream, if the actual last modified date is later, a 409 response is returned
	 *
	 * Examples
	 * 		PUT: /objects/demo:29?label=Updated
	 * 		PUT: /objects/demo:29?state=D?logMessage=Deleted
	 */
	public function modify_object($pid, $label = false, $ownerId = false, $state = false, $logMessage = false,  $lastModifiedDate = false){
		$args = array();

		if($label !== false){
			$args['label'] = $label;
		}

		if($ownerId !== false){
			$args['$ownerId'] = $ownerId;
		}
		if($state !== false){
			$args['state'] = $state;
		}
		if($logMessage !== false){
			$args['logMessage'] = $logMessage;
		}
		if($lastModifiedDate !== false){
			$args['lastModifiedDate'] = $lastModifiedDate;
		}
		$result = $this->execute_raw("objects/$pid", $args, 'PUT');
		return $result;
	}

	/**
	 * modifyDatastream
	 *
	 * URL Syntax
	 * 		/objects/{pid}/datastreams/{dsID} ? [dsLocation] [altIDs] [dsLabel] [versionable] [dsState] [formatURI] [checksumType] [checksum] [mimeType] [logMessage] [ignoreContent] [lastModifiedDate]
	 *
	 * HTTP Method
	 * 		PUT
	 *
	 * HTTP Response
	 * 		200
	 *
	 * Parameters
	 * 		Name  Description  Default  Options
	 * 		{pid}  persistent identifier of the digital object
	 * 		{dsID}  datastream identifier
	 * 		dsLocation  location of datastream content
	 * 		altIDs  alternate identifiers for the datastream
	 * 		dsLabel  the label for the datastream
	 * 		versionable  enable versioning of the datastream  the "versionable" property of the existing datastream  true, false
	 * 		dsState  one of "A", "I", "D" (*A*ctive, *I*nactive, *D*eleted)  A  A, I, D
	 * 		formatURI  the format URI of the datastream
	 * 		checksumType  the algorithm used to compute the checksum  DEFAULT  DEFAULT, DISABLED, MD5, SHA-1, SHA-256, SHA-385, SHA-512
	 * 		checksum  the value of the checksum represented as a hexadecimal string
	 * 		mimeType  the MIME type of the content being added, this overrides the Content-Type request header
	 * 		logMessage  a message describing the activity being performed
	 * 		ignoreContent  tells the request handler to ignore any content included as part of the request, indicating that you do not intend to update the datasteam content. This is primarily provided to allow the use of client tools which always require content to be included as part of PUT requests.  false  true, false
	 * 		lastModifiedDate  date/time of the last (known) modification to the datastream, if the actual last modified date is later, a 409 response is returned
	 * 		multipart file as request content  file to replace existing datastream (for Managed datastreams)
	 *
	 * Examples
	 * 		PUT: /objects/demo:35/datastreams/HIGH (with Multipart file)
	 * 		PUT: /objects/demo:35/datastreams/HIGH?dsLocation=http://example:80/highDS?logMessage=Update
	 *
	 */
	public function modify_datastream($pid, $dsID, $dsLabel, $content=false, $mimeType = false, $versionable=false, $dsState=false, $dsLocation=false, $altIDs = false, $formatURI = false, $checksum = false, $logMessage = false, $ignoreContent = false, $lastModifiedDate = false){

		$ignoreContent = $content ? $ignoreContent : true;
		$data_to_send = $content ? $content : null;
		$mimeType = $mimeType ? $mimeType : '';

		$args = array();
		if($dsLocation !== false){
			$args['dsLocation'] = $dsLocation;
		}
		if($dsLabel !== false){
			$args['dsLabel'] = $dsLabel;
		}
		if($altIDs !== false){
			$args['altIDs'] = $altIDs;
		}
		$args['versionable'] = $versionable;

		if($dsState !== false){
			$args['dsState'] = $dsState;
		}
		if($formatURI !== false){
			$args['formatURI'] = $formatURI;
		}
		if($checksum !== false){
			$args['checksum'] = $checksum;
		}
		if($logMessage !== false){
			$args['logMessage'] = $logMessage;
		}
		if($ignoreContent !== false){
			$args['ignoreContent'] = $ignoreContent;
		}
		if($lastModifiedDate !== false){
			$args['lastModifiedDate'] = $lastModifiedDate;
		}
		$result = $this->execute_raw("objects/$pid/datastreams/$dsID", $args, 'PUT', $data_to_send, $mimeType);
		return $result;
	}

	/**
	 * Returns true if the object is to be returned by queries. False otherwise.
	 * @param $node
	 */
	protected function accept_object($node){
		//if($this->get_config()->get_return_system_objects()){
		return true;
		//}
		//$pid = $this->xml_value($node, 'pid');
		//if(strlen($pid)<= strlen(self::FEDORA_SYSTEM)){
		//	return true;
		//}
		//return strtolower(substr($pid, 0, strlen(self::FEDORA_SYSTEM))) != self::FEDORA_SYSTEM;
	}

	/**
	 * Returns true if the node has XML node children.
	 * As opposed to character/attribute/whatever nodes.
	 * @param DOMNode $p
	 */
	function node_has_children($p) {
		if($p->hasChildNodes()) {
			foreach ($p->childNodes as $c) {
				if ($c->nodeType == XML_ELEMENT_NODE)
				return true;
			}
		}
		return false;
	}

	protected function xml_to_array($document, $node){
		if($node instanceof DOMCharacterData){
			return $node->nodeValue;
		}
		$xpath = new DOMXPath($document);
		$object = array();
		$fields = $xpath->query('./*', $node);
		if($fields->length > 0){
			foreach($fields as $field){
				if(!$this->node_has_children($field)){
					$field_name = strtolower($field->nodeName);
					if(!empty($field_name)){
						$value = $field->nodeValue;
						$f = strtolower($field_name);
						if($f == 'created' || $f == 'modified' || $f == 'createddate' || $f == 'lastmodifieddate' || $f == 'objcreatedate' || $f == 'objlastmoddate'){
							$value = self::parse_date($value);
						}
						if(isset($object[$field_name])){
							$object[$field_name] = array_merge((array)$object[$field_name], (array)$value);
						}else{
							$object[$field_name] = $value;
						}
					}
					if(empty($field->nodeValue)){
						$attributes = $field->attributes;
						for($i = 0; $i<$attributes->length; $i++){
							$a = $attributes->item($i);
							$name = '@'. strtolower($a->nodeName);
							$value = array($name=>$a->nodeValue);
							if(isset($object[$field_name])){
								$object[$field_name] = array_merge((array)$object[$field_name], (array)$value);
							}else{
								$object[$field_name] = $value;
							}
						}
					}
				}else {
					$field_name = $field->nodeName;
					$value = $this->xml_to_array($document, $field);
					$value = empty($value) ? '' : $value;
					$object[$field_name] = $value;
				}
			}
		}
		return $object;
	}
}



















?>