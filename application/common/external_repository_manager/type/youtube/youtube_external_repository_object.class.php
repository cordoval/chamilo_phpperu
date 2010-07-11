<?php
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_object.class.php';

class YoutubeExternalRepositoryObject extends StreamingMediaObject
{
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_TAGS = 'tags';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FAILED = 'failed';
    const STATUS_PROCESSING = 'processing';

    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    function set_category($category)
    {
        return $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    function get_tags()
    {
        return $this->get_default_property(self :: PROPERTY_TAGS);
    }

    function get_type()
    {
        return 'youtube';
    }

    function set_tags($tags)
    {
        return $this->set_default_property(self :: PROPERTY_TAGS, $tags);
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CATEGORY, self :: PROPERTY_TAGS));
    }

    public function get_status_text()
    {
        $status = $this->get_status();
        switch ($status)
        {
            case self :: STATUS_REJECTED :
                return Translation :: get('Rejected');
                break;
            case self :: STATUS_PROCESSING :
                return Translation :: get('Processing');
                break;
            case self :: STATUS_FAILED :
                return Translation :: get('Failed');
                break;
            case self :: STATUS_AVAILABLE :
                return Translation :: get('Available');
                break;
            default :
                return Translation :: get('Unknown');
        }
    }
}
?>