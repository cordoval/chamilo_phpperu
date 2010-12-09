<?php
namespace common\extensions\external_repository_manager\implementation\box;

use common\extensions\external_repository_manager\ExternalRepositoryObject;
use repository\RepositoryDataManager;

class BoxExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'box';

    const PROPERTY_NAME = 'name'; 
    const PROPERTY_SIZE = 'size';    
	    
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
    
	function get_content_data($external_object)
	{		
		$external_repository = RepositoryDataManager :: get_instance()->retrieve_external_repository($this->get_external_repository_id());				
		return BoxExternalRepositoryConnector :: get_instance($external_repository)->download_external_repository_object($external_object);
	}
}
?>