<?php
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_external_repository_object.class.php';

class DropIoExternalRepositoryObject extends StreamingMediaExternalRepositoryObject
{
    const OBJECT_TYPE = 'drop_io';

    const PROPERTY_VERSION = 'version';
    const PROPERTY_FORMAT = 'format';
    const PROPERTY_NAME = 'name';
    const PROPERTY_EXPIRATION_LENGTH = 'expiration_length';

    function get_version()
    {
    	return $this->get_default_property(self :: PROPERTY_VERSION);
    }
    
    function set_version($version)
    {
    	$this->set_default_property(self :: PROPERTY_VERSION, $version);
    }
    
        function get_format()
    {
    	return $this->get_default_property(self :: PROPERTY_FORMAT);
    }
    
    function set_format($format)
    {
    	$this->set_default_property(self :: PROPERTY_FORMAT, $format);
    }
    
        function get_name()
    {
    	return $this->get_default_property(self :: PROPERTY_NAME);
    }
    
    function set_name($name)
    {
    	$this->set_default_property(self :: PROPERTY_NAME, $name);
    }
        
    function get_expiration_length()
    {
    	return $this->get_default_property(self :: PROPERTY_EXPIRATION_LENGTH);
    }
    
    function set_expiration_length($expiration)
    {
    	$this->set_default_property(self :: PROPERTY_EXPIRATION_LENGTH, $expiration);
    }
    
    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>