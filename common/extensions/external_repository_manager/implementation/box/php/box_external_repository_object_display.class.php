<?php
namespace common\extensions\external_repository_manager\implementation\box;

use common\libraries\Translation;
use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

class BoxExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{		
	function get_display_properties()
    {
        $object = $this->get_object();

        $properties = parent :: get_display_properties();        
        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {   
    }
}
?>