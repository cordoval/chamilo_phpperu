<?php
/**
 *
 * @author magali.gillard
 *
 */
class PhotobucketExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'photobucket';

    const PROPERTY_TAGS = 'tags'; 
    const PROPERTY_URL = 'url';
    const PROPERTY_THUMBNAIL = 'thumbnail';
	    
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
		return implode(", ", $this->get_tags());
    }
    
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TAGS, self :: PROPERTY_URL, self :: PROPERTY_THUMBNAIL));
    }

    static function get_object_type()
    {
    	return self :: OBJECT_TYPE;
	}
	
    /**
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }
    
    /**
     * @return the $url
     */
    public function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }
    
    /**
     * @return the $thumbnail
     */
    public function get_thumbnail()
    {
        return $this->get_default_property(self :: PROPERTY_THUMBNAIL);
    }

    /**
     * @param $thumbnail the $thumbnail to set
     */
    public function set_thumbnail($thumbnail)
    {
        $this->set_default_property(self :: PROPERTY_THUMBNAIL, $thumbnail);
    }
}
?>