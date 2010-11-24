<?php
namespace common\extensions\video_conferencing_manager\implementation\dimdim;
use common\extensions\video_conferencing_manager\VideoConferencingMeetingObject;

class DimdimVideoConferencingMeetingObject extends VideoConferencingMeetingObject
{
    const OBJECT_TYPE = 'vimeo';

    const PROPERTY_URLS = 'urls';
    const PROPERTY_TAGS = 'tags';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS, self :: PROPERTY_TAGS));
    }

    function get_urls()
    {
        return $this->get_default_property(self :: PROPERTY_URLS);
    }

    function set_urls($urls)
    {
        return $this->set_default_property(self :: PROPERTY_URLS, $urls);
    }

    function get_tags()
    {
        return $this->get_default_property(self :: PROPERTY_TAGS);
    }

    function set_tags($tags)
    {
        return $this->set_default_property(self :: PROPERTY_TAGS, $tags);
    }

    function get_tags_string($include_links = true)
    {
        $tags = array();
        foreach ($this->get_tags() as $tag)
        {
            if ($include_links)
            {
                $tags[] = '<a href="http://www.vimeo.com/tag:' . $tag->normalized . '">' . $tag->_content . '</a>';
            }
            else
            {
                $tags[] = $tag->_content;
            }
        }

        return implode(', ', $tags);
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
    
    function is_usable()
    {
        return $this->get_right(self :: RIGHT_USE);
    }
}
?>