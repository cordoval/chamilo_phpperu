<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

use common\libraries\Translation;
use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

class DropboxExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{		
	function get_display_properties()
    {
        $object = $this->get_object();

        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Name')] = $object->get_name();
        $properties[Translation :: get('Size')] = $object->get_size();
        $properties[Translation :: get('Modified')] = $object->get_modified();

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