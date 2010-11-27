<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

use common\extensions\external_repository_manager\ExternalRepositoryObject;
use repository\RepositoryDataManager;

class DropboxExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'dropbox';

    const PROPERTY_NAME = 'name';
    const PROPERTY_SIZE = 'size';
    const PROPERTY_MODIFIED = 'modified';

	function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function set_name($name)
    {
        return $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    static function get_object_type()
    {
    	return self :: OBJECT_TYPE;
	}

    public function get_size()
    {
        return $this->get_default_property(self :: PROPERTY_SIZE);
    }

    public function get_modified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    public function set_modified($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

	function get_content_data($external_object)
	{
		$external_repository = RepositoryDataManager :: get_instance()->retrieve_external_instance($this->get_external_repository_id());
		$test = DropboxExternalRepositoryConnector :: get_instance($external_repository)->download_external_repository_object($external_object);
		//dump($test);
		return $test;
	}
}
?>