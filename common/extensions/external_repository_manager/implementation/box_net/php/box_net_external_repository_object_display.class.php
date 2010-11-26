<?php
namespace common\extensions\external_repository_manager\implementation\box_net;

use common\libraries\Translation;
use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

class BoxNetExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
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
    }
}
?>