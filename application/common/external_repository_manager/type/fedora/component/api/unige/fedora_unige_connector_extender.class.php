<?php

/**
 * Extender for FedoraExternalRepositoryConnector.
 * Provides method's specialization for the standard fedora connector.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraUnigeExternalRepositoryConnectorExtender{

	/**
	 * @var FedoraExternalRepositoryConnector
	 */
	private $connector;

	function __construct($connector){
		$this->connector = $connector;
	}

	/**
	 * @return FedoraExternalRepositoryConnector
	 */
	public function get_connector(){
		return $this->connector;
	}

	public function get_fedora(){
		return $this->connector->get_fedora();
	}

	public function get_store(){
		$result =  $this->connector->get_default_store();
		$disciplines = $this->get_discipline_tree();
		$owner = $this->connector->get_owner_id();
		$fs = fedora_fs_subject::factory($disciplines, $owner);
		$subject = new fedora_fs_store(Translation::get('Subject'), 'subject');
		$subject->add_all($fs);
		$result->add($subject);
		return $result;
	}



	/**
	 * Returns disciplines formated as a tree.
	 *
	 */
	public function get_discipline_tree(){
		$disciplines = $this->retrieve_disciplines();

		$lang = Translation::get_language();

		foreach($disciplines as $key => &$discipline){
			$discipline['id'] = $key;
			$discipline['title'] = $discipline[$lang];
		}

		foreach($disciplines as $discipline){
			$parent = $discipline['parent'];
			$id = $discipline['id'];
			$disciplines[$parent]['sub'][$id] = $discipline;
		}

		foreach($disciplines as &$discipline){
			if(isset($discipline['sub'])){
				$children = $discipline['sub'];
				asort($children);
				$discipline['sub'] = $children;
			}else{
				$discipline['sub'] = null;
			}
		}
		$result = array();
		foreach($disciplines as $discipline){
			if(empty($discipline['parent'])){
				$result[] = $discipline;
			}
		}

		return $result;
	}

	/**
	 * Retrieve disclipline(s).
	 *
	 * @param string $id
	 * @return array
	 */
	public function retrieve_disciplines($id=false){
		$fedora = $this->get_fedora();
		$result = $fedora->SWITCH_get_disciplines($id);
		return $result;
	}

	/**
	 * Retrieve license(s)
	 *
	 * @param string $id
	 * @return array
	 */
	public function retrieve_licenses($id=false){
		$fedora = $this->get_fedora();
		$result = $fedora->SWITCH_get_licenses($id);

		return $result;
	}

	private $rights = false;
	public function retrieve_rights($id=false){
		if(empty($rights)){
			$result = array();
			$result['public'] = Translation::get('Public');
			$result['institution'] = Translation::get('Institution');
			$result['private'] = Translation::get('Private');
			$this->rights = $result;
		}
		$result = $id ? $this->rights[$id] : $this->rights;
		return $result;
	}

	/**
	 * Returns object's metadata
	 *
	 * @param unknown_type $pid
	 */
	public function retrieve_object_metadata($pid){
		return $this->retrieve_object_chor_dc($pid);
	}

	/**
	 * Retrieve the object's chor_dc datastream content and returns it formatted as an array.
	 * Returns an empty array if the chor_dc datastream doesn't exist.
	 *
	 * @param string $pid
	 * @return array
	 */
	public function retrieve_object_chor_dc($pid){
		$result = array();
		try{
			$ds = $this->connector->retrieve_datastream_content($pid, 'CHOR_DC');
			$doc = new DOMDocument();
			$doc->loadXML($ds);
			$nodes = $doc->documentElement->childNodes;

			//$doc->documentElement->prefix
			foreach($nodes as $node){
				$prefix = $node->prefix ? $node->prefix.':' : '';
				$name = str_replace($prefix, '', $node->tagName);
				switch($name){
					case 'title':
					case 'creator':
						$value = $node->nodeValue;
						if($value){
							$result[$name] = $value;
						}
						break;
					case 'rights':
					case 'accessRights':
						$value = $node->getAttribute('chor_dcterms:access');
						if($value){
							$result[$name] = $value;
						}
						break;
					case 'license':
						$value = $node->getAttribute('xsi:type');
						if($value){
							$result[$name] = $value;
							$license = $this->retrieve_licenses($value);
							$lang = Translation::get_language();
							$text = isset($license[$lang]) ? $license[$lang] : '';
							if($text){
								if(substr($value, 0, 4) == 'http'){
									$value = '<a href="' . $value . '" target="_blank">' . $text .'</a>';
								}else{
									$value = $text;
								}
							}else{
								$value = $value;
							}
							$result[$name .'_text'] = $value;
						}
						break;
					case 'subject':
						$value = $node->getAttribute('chor_dcterms:discipline');
						if($value){
							$result[$name] = $value;
							$discipline = $this->retrieve_disciplines($value);
							$lang = Translation::get_language();
							$value = isset($discipline[$lang]) ? $discipline[$lang] : $value;
							$result[$name .'_text'] = $value;
						}
						break;
					case 'description':
						$value = $node->nodeValue;
						if($value){
							$result[$name] = $value;
						}

						break;
					default:
						$value = '';
						break;
				}
			}
			return $result;
		}catch(Exception $e){
			$result = array();
		}
		return $result;
	}
/*
	function __call($name, $args){
		$f = array($this->connector, $name);
		return is_callable($f) ? call_user_func_array($f, $args) : false;
	}
*/
}




























