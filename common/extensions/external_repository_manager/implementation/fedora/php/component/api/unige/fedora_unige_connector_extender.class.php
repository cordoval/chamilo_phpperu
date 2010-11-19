<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Translation;
use common\libraries\Utilities;



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

	const DOCUMENTS_PUBLIC = 'public';
	const DOCUMENTS_INSTITUTION = 'institution';
	const DOCUMENTS_PRIVATE = 'private';

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
		$result =  $this->get_default_store();
		$disciplines = $this->get_discipline_tree();
		$owner = $this->connector->get_owner_id();
		$fs = fedora_fs_subject::factory($disciplines, $owner);
		$subject = new fedora_fs_store(Translation::get('Subject'), 'subject');
		$subject->add_all($fs);
		$result->add($subject);
		return $result;
	}

	public function get_default_store(){
		$owner = $this->get_connector()->get_owner_id();

		$today = today();
		$this_week = this_week();
		$last_week = last_week();
		$two_weeks_ago = last_week(2);
		$three_weeks_ago = last_week(3);

		$result = new fedora_fs_store(Translation::get_instance()->translate('root'), 'root');

		$result->add($mystuff = new fedora_fs_store(Translation::get('mystuff'), FedoraExternalRepositoryConnector::DOCUMENTS_MY_STUFF));
		$mystuff->set_class('user');
		$mystuff->aggregate(new fedora_fs_mystuff('_'.FedoraExternalRepositoryConnector::DOCUMENTS_MY_STUFF, '', $owner));
		$mystuff->add($rights = new fedora_fs_store('rights', translation::get('Rights'), 'rights'));
		$rights->add($fs = new fedora_fs_access_right('public', translation::get('Public'), $owner, 'green_light', self::DOCUMENTS_PUBLIC));
		$rights->add(new fedora_fs_access_right('institution', translation::get('Institution'), $owner, 'yellow_light', self::DOCUMENTS_INSTITUTION));
		$rights->add(new fedora_fs_access_right('private', translation::get('Private'), $owner, 'red_light', self::DOCUMENTS_PRIVATE));

		$mystuff->add($history = new fedora_fs_store(Translation::get_instance()->translate('history')));
		$history->add(new fedora_fs_history(Translation::get('today'), today(), NULL, $owner, FedoraExternalRepositoryConnector::DOCUMENTS_TODAY));
		$history->add(new fedora_fs_history(Translation::get('this_week'), $this_week, NULL, $owner, FedoraExternalRepositoryConnector::DOCUMENTS_THIS_WEEK));
		$history->add(new fedora_fs_history(Translation::get('last_week'), $last_week, $this_week, $owner, FedoraExternalRepositoryConnector::DOCUMENTS_LAST_WEEK));
		$history->add(new fedora_fs_history(Translation::get('two_weeks_ago'), $two_weeks_ago, $last_week, $owner, FedoraExternalRepositoryConnector::DOCUMENTS_TWO_WEEKS_AGO));
		$history->add(new fedora_fs_history(Translation::get('three_weeks_ago'), $three_weeks_ago, $two_weeks_ago, $owner, FedoraExternalRepositoryConnector::DOCUMENTS_THREE_WEEKS_AGO));

		$result->aggregate(new fedora_fs_lastobjects('', false, $owner));

		$history->set_class('fedora_history');

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

	public function retrieve_collections($id=false){
		$result = array();
		$result['info:fedora/unigelom:learning_objects'] = 'Unige';
		if($key){
			return isset($result[$id]) ? $result[$id] : false;
		}else{
			return $result;
		}
	}

	private $rights = false;
	public function retrieve_rights($id=false){
		if(empty($this->rights)){
			$result = array();
			$fedora = $this->get_fedora();
			$rights = $fedora->SWITCH_get_rights($id);
			$lang = Translation::get_language();
			foreach($rights as $key => $right){
				$result[$key] = isset($right[$lang]) ? $right[$lang] : $right['eng'];
			}
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
		$result =  $this->retrieve_object_rels_ext($pid);
		return $result;
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
							$value = isset($license[$lang]) ? $license[$lang] : '';
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


	/**
	 *
	 *
	 *
	 * @param $pid
	 */
	public function retrieve_object_rels_ext($pid){
		$result = array();
		try{
			$ds = $this->connector->retrieve_datastream_content($pid, 'RELS_EXT');
			$doc = new DOMDocument();
			$doc->loadXML($ds);
			$nodes = $doc->documentElement->childNodes;

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
							$value = isset($license[$lang]) ? $license[$lang] : '';
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

					case 'isMemberOfCollection':
						$result['collection'] = $node->get('rdf:resource');

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

	public function determine_rights(fedora_fs_object $item){
		$can_edit = $item->get_owner() == FedoraExternalRepositoryConnector::get_owner_id();
		if(! $can_edit){
			$meta = $this->retrieve_object_metadata($item->get_pid());
			$edit_rights = isset($meta['rights']) ? $meta['rights'] : '';
			if($edit_rights == 'public' || $edit_rights == 'institution' ){
				$can_edit = true;
			}
		}

		$rights = array();
		$rights[ExternalRepositoryObject::RIGHT_USE] = true;
		$rights[ExternalRepositoryObject::RIGHT_EDIT] = $can_edit;
		$rights[ExternalRepositoryObject::RIGHT_DELETE] = $can_edit;
		$rights[ExternalRepositoryObject::RIGHT_DOWNLOAD] = true;
		return $rights;
	}

}




























