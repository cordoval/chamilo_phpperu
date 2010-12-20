<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\extensions\external_repository_manager\ExternalRepositoryObject;
use repository\ExternalRepository;
use common\extensions\external_repository_manager\ExternalRepositoryConnector;
use repository\RepositoryDataManager;
use common\extensions\external_repository_manager\ExternalRepositoryManagerConnector;

/**
 * Describes a Fedora object located in the Fedora repository.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryObject extends ExternalRepositoryObject
{

    private static $empty = null;

    /**
     * Empty object. I.e. placehoder for an object that does not exists.
     * For example because it was deleted in the exernal repository but not the synchronization data.
     *
     * @return FedoraExternalRepositoryObject
     */
    public static function get_empty()
    {
        if (empty(self :: $empty))
        {
            self :: $empty = new FedoraExternalRepositoryObject();
        }
        return self :: $empty;
    }

    const OBJECT_TYPE = 'fedora';

    const PROPERTY_AUTHOR = 'author';
    const PROPERTY_SUBJECT = 'subject';
    const PROPERTY_COLLECTION = 'collection';
    const PROPERTY_LICENSE = 'license';
    const PROPERTY_ACCESS_RIGHTS = 'access_rights';
    const PROPERTY_EDIT_RIGHTS = 'edit_rights';
    const PROPERTY_STATE = 'state';

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    /**
     * @param array $default_properties
     */
    function __construct($default_properties = array())
    {
        parent :: __construct($default_properties);

        if (! isset($default_properties[self :: PROPERTY_TYPE]))
        {
            $this->set_type(self :: get_object_type());
        }
    }

    private $_connector = false;

    public function get_connector()
    {
        if ($this->_connector)
        {
            return $this->_connector;
        }

        $repository_id = $this->get_external_repository_id();
        $repository = RepositoryDataManager :: get_instance()->retrieve_content_object($repository_id);
        $this->_connector = ExternalRepositoryManagerConnector :: get_instance($repository);
        return $this->_connector;
    }

    function get_license()
    {
        $name = str_replace('get_', '', __FUNCTION__);
        $result = $this->get_metadata($name);
        return $result;
    }

    function get_license_text()
    {
        $name = str_replace('get_', '', __FUNCTION__);
        $result = $this->get_metadata($name);
        return $result;
    }

    function get_author()
    {
        $name = str_replace('get_', '', __FUNCTION__);
        $result = $this->get_metadata($name);
        return $result;
    }

    function get_creator()
    {
        $name = str_replace('get_', '', __FUNCTION__);
        $result = $this->get_metadata($name);
        return $result;
    }

    /**
     * @return string
     */
    public function get_description()
    {
        $name = str_replace('get_', '', __FUNCTION__);
        $result = $this->get_metadata($name);
        return $result;
    }

    function get_subject()
    {
        $name = str_replace('get_', '', __FUNCTION__);
        $result = $this->get_metadata($name);
        return $result;
    }

    function get_subject_text()
    {
        $name = str_replace('get_', '', __FUNCTION__);
        $result = $this->get_metadata($name);
        return $result;
    }

    function get_edit_rights()
    {
        $name = 'rights';
        $result = $this->get_metadata($name);
        return $result;
    }

    function get_access_rights()
    {
        $name = 'accessRights';
        $result = $this->get_metadata($name);
        return $result;
    }

    /**
     * @return string
     */
    function get_resource_id()
    {
        return url_encode($this->get_type() . ':' . $this->get_id());
    }

    function get_content_data($export_format)
    {
        switch ($this->get_type())
        {
            case 'document' :
                $url = $this->get_content() . '&exportFormat=' . $export_format;
                break;
            case 'presentation' :
                $url = $this->get_content() . '&exportFormat=' . $export_format;
                break;
            case 'spreadsheet' :
                $url = $this->get_content() . '&fmcmd=' . $export_format;
                break;
            default :
                // Get the document's content link entry.
                //return array('pdf');
                break;
        }

        $external_repository = RepositoryDataManager :: get_instance()->retrieve_external_instance($this->get_external_repository_id());
        return FedoraExternalRepositoryManagerConnector :: get_instance($external_repository)->download_external_repository_object($url);
    }

    protected $metadata = false;

    function get_metadata($name = '')
    {
        if (! $this->metadata)
        {
            $connector = $this->get_connector();
            $this->metadata = $connector->retrieve_object_metadata($this->get_id());
        }
        if ($name)
        {
            $result = isset($this->metadata[$name]) ? $this->metadata[$name] : '';
        }
        else
        {
            $result = $this->metadata;
        }
        return $result;
    }

    function set_metadata($value)
    {
        $this->metadata = $value;
    }

    protected $datastreams = false;

    function get_datastreams($dsID = false)
    {
        $result = $this->datastreams;
        if (empty($result))
        {
            $connector = $this->get_connector();
            $result = $this->datastreams = $connector->retrieve_datastreams($this->get_id());
        }
        if ($dsID)
        {
            $result = isset($result[$dsID]) ? $result[$dsID] : false;
        }
        return $result;
    }

    /**
     * Set during initial creation to avoid chatty calls
     * @param $value
     */
    function set_datastreams($value)
    {
        $this->datastreams = $value;
    }

    function has_datastream($dsID)
    {
        return $this->get_datastreams($dsID) ? true : false;
    }

    function get_datastream_content($dsID)
    {
        $connector = $this->get_connector();
        $result = $connector->retrieve_datastream_content($this->get_id(), $dsID);
        return $result;
    }

    function get_datastream_content_url($dsID)
    {
        $connector = $this->get_connector();
        $result = $connector->get_datastream_content_url($this->get_id(), $dsID);
        return $result;
    }

    protected $state = 'A';

    function get_state()
    {
        return $this->state;
    }

    function set_state($value)
    {
        $this->state = $value;
    }

    function is_active()
    {
        return strtoupper($this->state) == 'A';
    }

    /**
     * If true the object is an empty object. I.e. it does not contain any real data.
     *
     * @return bool
     */
    function is_empty()
    {
        return count($this->get_default_properties());
    }

}

?>