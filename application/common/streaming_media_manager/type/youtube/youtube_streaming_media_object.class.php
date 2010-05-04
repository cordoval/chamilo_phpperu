<?php
require_once dirname(__FILE__) . '/../../streaming_media_object.class.php';
class YoutubeStreamingMediaObject extends StreamingMediaObject
{
	const PROPERTY_CATEGORY = 'category';
	const PROPERTY_TAGS = 'tags';
	
	function get_category()
    {
        return $this->get_additional_property(self :: PROPERTY_CATEGORY);
    }

    function set_category($category)
    {
        return $this->set_additional_property(self :: PROPERTY_CATEGORY, $category);
    }
    
	function get_tags()
    {
        return $this->get_additional_property(self :: PROPERTY_TAGS);
    }
    
    function get_type()
    {
        return 'youtube';
    }

    function set_tags($tags)
    {
        return $this->set_additional_property(self :: PROPERTY_TAGS, $tags);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_CATEGORY, self :: PROPERTY_TAGS);
    }
}
?>