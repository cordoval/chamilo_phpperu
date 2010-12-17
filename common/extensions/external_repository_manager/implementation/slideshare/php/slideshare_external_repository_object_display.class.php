<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;

require_once Path::get_common_extensions_path () . 'external_repository_manager/php/general/streaming/streaming_media_external_repository_object_display.class.php';

class SlideshareExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{
	
	function get_title() 
	{
		$object = $this->get_object();
		return '<h3>' . $object->get_title() . ' (' . Utilities::format_seconds_to_minutes($object->get_duration()) . ')</h3>';
	}

    function get_display_properties()
    {
        $properties = parent :: get_display_properties();

        return $properties;
    }
    
	function get_preview($is_thumbnail = false) {
		$object = $this->get_object();
		$html = array();
		if ($is_thumbnail)
		{
			$html[] = '<img class="' . $class . '" src="' . $object->get_thumbnail() . '" />';
		}
		else
		{
			$html[] = $object->get_embed();
		}
		return implode("\n", $html);
	}
}
?>