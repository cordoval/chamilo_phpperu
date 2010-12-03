<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\TimeUtil;
use common\libraries\fedora_fs_object;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Session;
use common\libraries\fedora_fs_store;
use common\libraries\fedora_fs_mystuff;
use common\libraries\fedora_fs_history;
use common\libraries\fedora_fs_lastobjects;
use common\libraries\fedora_fs_datastream;
use common\libraries\fedora_fs_search;
use common\libraries\FedoraProxy;
use common\libraries\RestConfig;
use common\libraries\ArrayResultSet;
use common\libraries\Utilities;
use common\libraries\ImageManipulation;
use common\libraries\Filesystem;
use common\libraries\FoxmlReader;
use repository\ExternalSetting;
use user\UserDataManager;
use Exception;
use common\extensions\external_repository_manager\ExternalRepositoryManagerConnector;

require_once dirname(__FILE__) . '/fedora_external_repository_object.class.php';
require_once Path::get_common_libraries_class_path() . '/fedora/lib.php';
require_once Path::get_common_extensions_path() . '/external_repository_manager/php/external_repository_manager_connector.class.php';

/**
 * Main object to connect Chamilo to the Fedora repository.
 * If an API provides a method's specialization calls it instead.
 * The API Extender do not inherit from this class because the connector's factory is not accessible.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerConnector extends ExternalRepositoryManagerConnector {

    const DOCUMENTS_MY_STUFF = 'my_stuff';
    const DOCUMENTS_TODAY = 'today';
    const DOCUMENTS_THIS_WEEK = 'this_week';
    const DOCUMENTS_LAST_WEEK = 'last_week';
    const DOCUMENTS_TWO_WEEKS_AGO = 'two_weeks_ago';
    const DOCUMENTS_THREE_WEEKS_AGO = 'three_weeks_ago';
    /**
     * Returns the 'full' user language. That is not the iso code - en - but the full english name - i.e. english.
     * Translates only languages recognized by Switch - en, de, fr, it.
     * If not one of the Switch language returns the Chamilo's code.
     *
     * To add a language modify the fedora ressources and add the translation here.
     * Used to bridge between Chamilo ISO code and Switch full name definition.
     *
     * @todo: move it to Translation and add additional languages
     *
     * @return string
     */
    public static function get_full_language() {
        $result = Translation::get_language();
        switch ($result) {
            case 'en': return 'english';
            case 'de': return 'german';
            case 'it': return 'italian';
            case 'fr': return 'french';
            default : return $result;
        }
    }

    /**
     * Returns the external id for the current user. That is the one that is used to identify the current user on the Fedora server.
     * Used mostly to identify ownership.
     *
     */
    public static function get_owner_id() {
        static $result = false;
        if ($result) {
            return $result;
        }
        $user = UserDataManager::get_instance()->retrieve_user(Session::get_user_id());
        $result = $user->get_external_uid();
        $result = $result ? $result : $user->get_email();
        return $result;
    }

    /**
     * @param mixed $query
     * @return mixed
     */
    static function translate_search_query($query) {
        return $query;
    }

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function __construct($external_repository_instance) {
        parent::__construct($external_repository_instance);
    }

    private $_store = false;

    /**
     *
     * @return fedora_fs_store
     */
    public function get_store() {
        if ($this->_store) {
            return $this->_store;
        } else if ($result = $this->call_api(__FUNCTION__)) {
            return $this->_store = $result;
        } else {
            return $this->_store = $this->get_default_store();
        }
    }

    public function get_default_store() {
        $owner = self::get_owner_id();

        $today = TimeUtil::today();
        $this_week = TimeUtil::this_week();
        $last_week = TimeUtil::last_week();
        $two_weeks_ago = TimeUtil::last_week(2);
        $three_weeks_ago = TimeUtil::last_week(3);

        $result = new fedora_fs_store(Translation::get_instance()->translate('root'));
        $result->add(new fedora_fs_mystuff(self::DOCUMENTS_MY_STUFF, Translation::get('mystuff'), $owner));
        $result->add($history = new fedora_fs_store(Translation::get_instance()->translate('history')));
        $history->add(new fedora_fs_history(Translation::get('today'), TimeUtil::today(), null, $owner, self::DOCUMENTS_TODAY));
        $history->add(new fedora_fs_history(Translation::get('this_week'), $this_week, null, $owner, self::DOCUMENTS_THIS_WEEK));
        $history->add(new fedora_fs_history(Translation::get('last_week'), $last_week, $this_week, $owner, self::DOCUMENTS_LAST_WEEK));
        $history->add(new fedora_fs_history(Translation::get('two_weeks_ago'), $two_weeks_ago, $last_week, $owner, self::DOCUMENTS_TWO_WEEKS_AGO));
        $history->add(new fedora_fs_history(Translation::get('three_weeks_ago'), $three_weeks_ago, $two_weeks_ago, $owner, self::DOCUMENTS_THREE_WEEKS_AGO));

        $result->aggregate(new fedora_fs_lastobjects('', false, $owner));

        $history->set_class('fedora_history');
        return $result;
    }

    /**
     * Returns the RestConfig fedora config entered by the plugin. Used by the fedora proxy to connect to the Fedora server.
     *
     * @param $external_repository_id
     * @return RestConfig
     */
    public function get_fedora_config($external_repository_id = false) {
        if (empty($external_repository_id)) {
            $external_repository_id = $this->get_external_repository_instance_id();
        }
        $url = ExternalSetting :: get('Url', $external_repository_id);
        $client_certificate_file = ExternalSetting :: get('ClientCertificateFile', $external_repository_id);
        $client_certificate_key_file = ExternalSetting :: get('ClientCertificateKeyFile', $external_repository_id);
        $client_certificate_key_password = ExternalSetting :: get('ClientCertificateKeyPassword', $external_repository_id);
        $check_target_certificate = ExternalSetting :: get('CheckTargetCertificate', $external_repository_id);
        $target_ca_file = ExternalSetting :: get('TargetCaFile', $external_repository_id);
        $basic_login = ExternalSetting :: get('Login', $external_repository_id);
        $basic_password = ExternalSetting :: get('Password', $external_repository_id);
        $max_results = ExternalSetting :: get('MaxResults', $external_repository_id);

        $result = new RestConfig();
        $result->set_base_url($url);
        $result->set_client_certificate_file($client_certificate_file);
        $result->set_client_certificate_key_file($client_certificate_key_file);
        $result->set_client_certificate_key_password($client_certificate_key_password);
        $result->set_check_target_certificate($check_target_certificate);
        $result->set_target_ca_file($target_ca_file);
        $result->set_basic_login($basic_login);
        $result->set_basic_password($basic_password);
        $result->set_max_results($max_results);

        return $result;
    }

    private $_fedora = false;

    /**
     * Returns a fedora proxy.
     *
     * @param $external_repository_id
     * @return FedoraProxy
     */
    public function get_fedora($external_repository_id=false) {
        if ($this->_fedora) {
            return $this->_fedora;
        }
        $this->_fedora = new FedoraProxy($this->get_fedora_config($external_repository_id));
        return $this->_fedora;
    }

    private $_api = false;

    /**
     * Returns either an extender for the current object or null.
     * An extender provides specialization for several methods: get_licences, etc
     */
    public function get_api_extender() {
        if ($this->_api !== false) {
            return $this->_api;
        }

        $external_repository_id = $this->get_external_repository_instance_id();
        $api_name = ExternalSetting :: get('Api', $external_repository_id);
        if ($api_name) {
            $path = dirname(__FILE__) . '/component/api/' . $api_name . '/fedora_' . $api_name . '_connector_extender.class.php';
            require_once $path;

            $class = __NAMESPACE__ . '\Fedora' . ucfirst($api_name) . 'ExternalRepositoryConnectorExtender';
            return $this->_api = new $class($this);
        } else {
            return $this->_api = null;
        }
    }

    /**
     * @param string $id
     * @param bool $return_active_only if true returns only active objects. That is objects with a state equals to A. If false returns both active, inactive and deleted objects;
     * @return FedoraExternalRepositoryObject|false
     *
     */
    function retrieve_external_repository_object($pid, $return_active_only = true) {
        $fedora = $this->get_fedora();
        try {
            $xml = $fedora->get_object_xml($pid);
        } catch (Exception $e) {
            //if object has been deleted throws HTTP 404, object not found
            $result = false;
        }
        $result = $xml ? $this->foxml_to_object($xml) : false;
        $result = $result && $return_active_only && (!$result->is_active()) ? false : $result;

        if ($result == false) {
            //garbage collector. If object has been deleted then delete possible synchronization data.
            FedoraExternalRepositoryManager::delete_synchronization_data($pid);
        }
        return $result;
    }

    /**
     * Returns all available datastreams for an object with $pid id.
     *
     * @param $pid
     * @return array
     */
    function retrieve_datastreams($pid) {
        $fedora = $this->get_fedora();
        $fs = new fedora_fs_object($pid, '', self::get_owner_id(), time(), time());
        $result = $fs->query($fedora);
        return $result;
    }

    /**
     * Returns the url used to access the datasream's content directly from Fedora.
     * Used for external access. I.e. harvester. Can be changed in ConnectorExtender.
     * Direct access to Fedora should not be provided to the end-user as it would bypass internal authentication.
     *
     * @param string $pid
     * @param string $dsID
     */
    function get_datastream_content_url($pid, $dsID) {
        if ($result = $this->call_api(__FUNCTION__, array($pid, $dsID))) {
            return $result;
        }

        $external_repository_id = $this->get_external_repository_instance_id();

        if ($url = ExternalSetting::get('ContentAccessUrl', $external_repository_id)) {
            $url = str_ireplace('$pid', $pid, $url);
            $url = str_ireplace('$dsID', $dsID, $url);
            return $url;
        } else {
            $fedora = $this->get_fedora();
            return $fedora->get_datastream_content_url($pid, $dsID);
        }
    }

    /**
     * Returns the content of a datastream
     *
     * @param string $pid object's id
     * @param string $dsID datatstream's id
     * @return string the datastream's content
     */
    public function retrieve_datastream_content($pid, $dsID) {
        $fedora = $this->get_fedora();
        return $fedora->get_datastream_content($pid, $dsID);
    }

    /**
     * Returns object's metadata from Fedora in an associative array.
     *
     * @param string $pid
     * @return array
     */
    public function retrieve_object_metadata($pid) {
        if ($result = $this->call_api(__FUNCTION__, array($pid))) {
            return $result;
        }

        //$result = $this->retrieve_object_chor_dc($pid);
        $fedora = $this->get_fedora();
        $profile = $fedora->get_object_profile($pid);
        $result = array();
        $result['title'] = $profile['objLabel'];
        $result['created'] = $profile['objCreateDate'];
        $result['modified'] = $profile['objLastModDate'];
        $result['state'] = $profile['objState'];
        $result['model'] = $profile['objModels'];

        return $result;
    }

    /**
     * Search for an object with a label=$label, owner=$owner and object belonging to $collection.
     *
     * @param string $label the object's label to search for
     * @param string $owner if provided search only for objects belonging to this owner. If true defaults to the current user.
     * @param string $collection  if provided search only for objects belonging to this collection @todo not yet implemented
     * @return false|array If found returns an array of object's properties. If not found returns false.
     */
    public function get_object_by_label($label, $owner=true, $collection='') {
        $fedora = $this->get_fedora();
        $owner = $owner === true ? $this->get_owner_id() : $owner;
        $result = $fedora->get_object_by_label($label, $owner, $collection);
        return $result;
    }

    /**
     * Returns the next available pid - i.e. object id - from the repository
     *
     * @return string
     */
    public function get_nextPID() {
        $fedora = $this->get_fedora();
        return $fedora->get_nextPID();
    }

    /**
     * Ingest, i.e. create, a new object.
     *
     * @param string $xml_content Foxml content
     * @param string $pid object's id
     * @param string $label object's label
     * @param string $owner object's owner, if not provided defaults to the current user
     */
    public function ingest($xml_content, $pid=0, $label='', $owner=false) {
        $owner = $owner ? $owner : $this->get_owner_id();
        $fedora = $this->get_fedora();
        return $fedora->ingest($xml_content, $pid, $label, $owner);
    }

    /**
     * Retrieve disclipline(s)
     * Can be overrided by the API
     * Default implementation returns nothing
     *
     * @param string $id
     * @return array
     */
    public function retrieve_disciplines($id=false) {
        $default = $id ? false : array();
        return $this->call_api(__FUNCTION__, array($id), $default);
    }

    /**
     * Retrieve license(s)
     * Can be overrided by the API
     * Default implementation returns nothing.
     *
     * @param string $id
     * @return array
     */
    public function retrieve_licenses($id=false) {
        $default = $id ? false : array();
        return $this->call_api(__FUNCTION__, array($id), $default);
    }

    /**
     * Retrieve rights(s)
     * Can be overrided by the API
     * Default implementation returns nothing
     *
     * @param string $id
     * @return array
     */
    public function retrieve_rights($id=false) {
        $default = $id ? false : array();
        $result = $this->call_api(__FUNCTION__, array($id), $default);
        return $result;
    }

    /**
     * Returns the list of collections
     *
     * @param $key $id
     */
    public function retrieve_collections($id=false) {
        $default = $id ? false : array();
        return $this->call_api(__FUNCTION__, array($id), $default);
    }

    /**
     * Mark an objec as deleted on the server.
     *
     * @param string $id
     */
    function delete_external_repository_object($id) {
        $fedora = $this->get_fedora();
        try {
            $result = $fedora->delete_object($id);
            FedoraExternalRepositoryManager::delete_synchronization_data($id);
            return true;
        } catch (Exception $e) {
            FedoraExternalRepositoryManager::delete_synchronization_data($id);
            return false;
        }
    }

    /**
     * Completely remove an object from the server.
     *
     *
     * @param unknown_type $id
     */
    function purge_external_repository_object($id) {
        $fedora = $this->get_fedora();
        try {
            $result = $fedora->purge_object($id);
            FedoraExternalRepositoryManager::delete_synchronization_data($id);
            return true;
        } catch (Exception $e) {
            FedoraExternalRepositoryManager::delete_synchronization_data($id);
            return false;
        }
    }

    /**
     * Export an object on the server to the repository.
     *
     * @see /component/exporter.php
     * @param ContentObject $content_object
     */
    function export_external_repository_object($co) {
        return false;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#count_external_repository_objects()
     */
    function count_external_repository_objects($condition) {
        $folder = Request::get(FedoraExternalRepositoryManager::PARAM_FOLDER);
        $store = $this->get_store();

        if ($condition) {
            $owner = $this->get_owner_id();
            $search = new fedora_fs_search('', $condition, fedora_fs_search::SEARCH_LEVEL_FUZZI, false, false, $owner, $count, $offset, $order_property);
            $result = $search->count($this->get_fedora());
            return $result;
        } else if ($folder) {
            $fs = $store->find($folder);
            $result = $fs->count($this->get_fedora());
            if ($fs instanceof fedora_fs_store) {
                $result -= count($fs->get_children());
            }
            return $result;
        } else {
            $result = $store->count($this->get_fedora());
            $result -= count($store->get_children());
            return $result;
        }
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_objects()
     */
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count) {
        $folder = Request::get(FedoraExternalRepositoryManager::PARAM_FOLDER);
        $store = $this->get_store();

        if ($order_property) {
            $sort = array();
            foreach ($order_property as $prop) {
                $name = $prop->get_property();
                $name = $name == 'title' ? 'label' : $name;
                $name = '$' . $name;
                $direction = $prop->get_direction();
                if ($direction == SORT_ASC) {
                    $sort[] = $name . ' asc';
                } else if ($direction == SORT_DESC) {
                    $sort[] = $name . ' desc';
                } else {
                    $sort[] = $name;
                }
            }
            $sort = implode(', ', $sort);
        } else {
            $sort = '';
        }

        $limit = $count;
        $offset = $offset;

        if ($condition) {
            $owner = $this->get_owner_id();
            $search = new fedora_fs_search('', $condition, fedora_fs_search::SEARCH_LEVEL_FUZZI, false, false, $owner, $order_property, $limit, $offset);
            $fedora = $this->get_fedora();
            $items = $search->query($fedora, $sort, $limit, $offset);

            foreach ($items as $item) {
                if ($item instanceof fedora_fs_object) {
                    $object = $this->fs_to_object($item);
                    $result[] = $object;
                }
            }
            $result = new ArrayResultSet($result);
        } else if ($folder) {
            $fs = $store->find($folder);
            $result = array();
            $fedora = $this->get_fedora();
            $items = $fs->query($fedora, $sort, $limit, $offset);
            foreach ($items as $item) {
                if ($item instanceof fedora_fs_object) {
                    $object = $this->fs_to_object($item);
                    $result[] = $object;
                }
            }
            $result = new ArrayResultSet($result);
        } else {
            $result = array();
            $fedora = $this->get_fedora();
            $items = $store->query($fedora, $sort, $limit, $offset);
            foreach ($items as $item) {
                if ($item instanceof fedora_fs_object) {
                    $object = $this->fs_to_object($item);
                    $result[] = $object;
                }
            }
            $result = new ArrayResultSet($result);
        }
        return $result;
    }

    /**
     * Update the Thumbnail datastream of an object. If file provided is greater than max size - 150 pixels - the image size is decreased.
     *
     * @param $pid object ID
     * @param $name image/datastream's name
     * @param $path path to the file
     * @param $mime_type mime type
     */
    function update_thumbnail($pid, $name, $path, $mime_type) {
        $content = $this->get_thumbnail_content($path);
        $fedora = $this->get_fedora();
        return $fedora->update_datastream($pid, 'THUMBNAIL', $name, $content, $mime_type, false);
    }

    /**
     * Returns image's content. If file provided is greater than max size - 150 pixels - the image size is decreased.
     *
     * @param $path
     */
    function get_thumbnail_content($path) {
        $max_size = 150;
        $size = getimagesize($path);
        $width = $size[0];
        $height = $size[1];
        if ($width == 0) {//Unable to deternime image size
            return file_get_contents($path);
        } else if ($width <= $max_size && $height <= $max_size) {
            return file_get_contents($path);
        } else {
            $tmp = Path::get_temp_path() . 'f' . Session::get_user_id() . md5(uniqid('fedora_thumb'));
            $ratio = $size[1] / $size[0];
            $ratio = $ratio ? $ratio : 1;
            $thumbnail_creator = ImageManipulation::factory($path);
            $thumbnail_creator->create_thumbnail($max_size, $ratio * $max_size);
            $thumbnail_creator->write_to_file($tmp);

            $content = file_get_contents($tmp);
            Filesystem::remove($tmp);
            return $content;
        }
    }

    /**
     * Creates a FedoraExternalRepositoryObject based on a fedora_fs_object definintion;
     *
     * @param $fs
     * @return FedoraExternalRepositoryObject
     */
    function fs_to_object(fedora_fs_object $item) {
        $object = new FedoraExternalRepositoryObject();
        $object->set_id($item->get_pid());
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($item->get_title());
        $object->set_created($item->get_created_date());
        $object->set_modified($item->get_modified_date());
        $object->set_owner_id($item->get_owner());
        $object->set_rights($this->determine_rights($item));
        return $object;
    }

    public function determine_rights(fedora_fs_object $item) {
        if ($result = $this->call_api(__FUNCTION__, $item)) {
            return $result;
        }

        $can_edit = $item->get_owner() == $this->get_owner_id();
        $rights = array();
        $rights[FedoraExternalRepositoryObject::RIGHT_USE] = true;
        $rights[FedoraExternalRepositoryObject::RIGHT_EDIT] = $can_edit;
        $rights[FedoraExternalRepositoryObject::RIGHT_DELETE] = $can_edit;
        $rights[FedoraExternalRepositoryObject::RIGHT_DOWNLOAD] = true;
        return $rights;
    }

    protected function call_api($name, $args = array(), $default = false) {
        $api = $this->get_api_extender();
        $args = is_array($args) ? $args : array($args);
        $f = array($api, $name);
        if (is_callable($f)) {
            return call_user_func_array($f, $args);
        } else {
            return $default;
        }
    }

    /**
     * Returns a FedoraExternalRepositoryObject from the foxml description.
     * Used to build an object in one call.
     *
     * @param string $xml
     * @return FedoraExternalRepositoryObject
     */
    protected function foxml_to_object($xml) {
        $result = new FedoraExternalRepositoryObject();
        $reader = new FoxmlReader($xml);

        $pid = $reader->PID;
        $properties = $reader->get_objectProperties();
        $label = $properties->get_label();
        $modified = $properties->get_lastModifiedDate();
        $created = $properties->get_createdDate();
        $owner = $properties->get_ownerId();
        $state = substr($properties->get_state(), 0, 1);

        $config = $this->get_fedora()->get_config();
        $base_url = rtrim($config->get_base_url(), '/');

        $metadata = array();
        $datastreams = array();
        $dss = $reader->list_datastream();
        foreach ($dss as $ds) {
            $dsID = $ds->ID;
            $version = $ds->first_datastreamVersion();
            $title = $version->LABEL;
            $mime_type = $version->MIMETYPE;
            $source = "$base_url/objects/$pid/datastreams/$dsID/content";
            $datastreams[$dsID] = new fedora_fs_datastream($pid, $dsID, $title, $mime_type, $source);

            if ($dsID == 'RELS-EXT') {
                $description = $version->children_head()->children_head()->children_head();
                $children = $description->children();
                foreach ($children as $child) {
                    $name = $child->name();
                    $value = $child->value();
                    $parts = explode(':', $name);
                    $namespace = reset($parts);
                    $name = end($parts);
                    if ($namespace == 'dcterms') {
                        $metadata[$name] = $value;
                    } else if ($name == 'isMemberOfCollection') {
                        $metadata['collection'] = $child->get('rdf:resource');
                    }
                }
                $metadata['title'] = $label;
                $metadata['created'] = $created;
                $metadata['modified'] = $modified;

                $lang = self::get_full_language();
                if ($id = $metadata['license']) {
                    $data = $this->retrieve_licenses($id);
                    $metadata['license_text'] = $data[$lang];
                }

                if ($id = $metadata['subject']) {
                    $data = $this->retrieve_disciplines($id);
                    $metadata['subject_text'] = $data[$lang];
                }
            }
        }

        $edit_right = isset($metadata['rights']) ? $metadata['rights'] : 'private';
        $can_edit = ($owner == $this->get_owner_id()) || $edit_right == 'public' || $edit_right == 'institution';

        $rights = array();
        $rights[FedoraExternalRepositoryObject::RIGHT_USE] = true;
        $rights[FedoraExternalRepositoryObject::RIGHT_EDIT] = $can_edit;
        $rights[FedoraExternalRepositoryObject::RIGHT_DELETE] = $can_edit;
        $rights[FedoraExternalRepositoryObject::RIGHT_DOWNLOAD] = true;

        $result->set_created($created);
        $result->set_id($pid);
        $result->set_title($label);
        $result->set_external_repository_id($this->get_external_repository_instance_id());
        $result->set_modified($modified);
        $result->set_datastreams($datastreams);
        $result->set_metadata($metadata);
        $result->set_owner_id($owner);
        $result->set_created($created);
        $result->set_rights($rights);
        $result->set_state($state);
        return $result;
    }

}

?>