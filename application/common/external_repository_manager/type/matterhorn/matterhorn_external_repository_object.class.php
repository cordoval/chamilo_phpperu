<?php
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_external_repository_object.class.php';

/**
 * 
 * @author magali.gillard
 *
 */
class MatterhornExternalRepositoryObject extends StreamingMediaExternalRepositoryObject
{
    const OBJECT_TYPE = 'matterhorn';

    const PROPERTY_SUBJECT = 'subject';
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_TYPE = 'type';

    const PROPERTY_STATUS = 'status';
    const STATUS_UNAVAILABLE = 'unavailable';
    const STATUS_AVAILABLE = 'available';

    function get_subject()
    {
        return $this->get_default_property(self :: PROPERTY_SUBJECT);
    }

    function set_subject($subject)
    {
        return $this->set_default_property(self :: PROPERTY_SUBJECT, $subject);
    }

   function get_language()
   {
   		return $this->get_default_property(self :: PROPERTY_LANGUAGE);
   }
  
    function set_language($language)
    {
    	return $this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
    }

	function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }
    
    function set_type($type)
    {
    	return $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }
    
    function get_type()
    {
    	return $this->get_default_property(self :: PROPERTY_TYPE);	
    }
    
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_SUBJECT, self :: PROPERTY_LANGUAGE, self :: PROPERTY_TYPE, self :: PROPERTY_STATUS));
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>