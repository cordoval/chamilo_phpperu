<?php
namespace common\extensions\external_repository_manager\implementation\photobucket;

use common\libraries\Translation;
use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

class PhotobucketExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{		
	function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Tags')] = $this->get_object()->get_tags_string();
        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
		
        $object = $this->get_object();
        $size = ($is_thumbnail ? $object->get_thumbnail() : $object->get_url());
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');
    	
    	$html = array();
        $html[] = '<img class="' . $class . '" src="' . $size . '" />';
        return implode("\n", $html);
    }
}
?>