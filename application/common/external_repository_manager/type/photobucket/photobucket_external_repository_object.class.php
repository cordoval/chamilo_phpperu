<?php
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_external_repository_object.class.php';

/**
 *
 * @author magali.gillard
 *
 */
class PhotobucketExternalRepositoryObject extends StreamingMediaExternalRepositoryObject
{
    const OBJECT_TYPE = 'photobucket';
	const PROPERTY_ALBUM = 'album';
    const PROPERTY_TAGS = 'tags';
	    
	function get_album()
	{
		return $this->get_default_property(self :: PROPERTY_ALBUM);
	}
	
	function set_album($album)
	{
		return $this->set_default_property(self :: PROPERTY_ALBUM, $album);
	}

    function get_album_string()
    {
		return implode(" ", $this->get_album());
    }

	function get_tags()
    {
        return $this->get_default_property(self :: PROPERTY_TAGS);
    }
    
    function set_tags($tags)
    {
        return $this->set_default_property(self :: PROPERTY_TAGS, $tags);
    }

    function get_tags_string()
    {
		return implode(" ", $this->get_tags());
    }
    
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ALBUM));
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
	}
}
?>