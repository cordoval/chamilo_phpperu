<?php
namespace common\extensions\video_conferencing_manager\implementation\dimdim;

use common\extensions\external_repository_manager\StreamingMediaExternalRepositoryObjectDisplay;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;

require_once Path::get_common_extensions_path () . 'external_repository_manager/php/general/streaming/streaming_media_external_repository_object_display.class.php';

class VimeoExternalRepositoryObjectDisplay extends StreamingMediaExternalRepositoryObjectDisplay
{
	
	function get_title() 
	{
		$object = $this->get_object();
		return '<h3>' . $object->get_title() . ' (' . Utilities::format_seconds_to_minutes($object->get_duration()) . ')</h3>';
	}

    function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Tags')] = $this->get_object()->get_tags_string();

        return $properties;
    }

//    function get_preview($is_thumbnail = false)
//    {
//        $object = $this->get_object();
//        $size = ($is_thumbnail ? VimeoExternalRepositoryObject :: SIZE_SQUARE : VimeoExternalRepositoryObject :: SIZE_MEDIUM);
//        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');
//
//        $html = array();
//        $html[] = '<img class="' . $class . '" src="' . $object->get_url($size) . '" />';
//        return implode("\n", $html);
//    }
    
    
	function get_preview($is_thumbnail = false) {
		$object = $this->get_object();
		$html = array();
		if ($is_thumbnail)
		{
			$html[] = '<img class="' . $class . '" src="' . $object->get_thumbnail() . '" />';
		}
		else
		{
			$html[] = '<iframe src="http://player.vimeo.com/video/' . $object->get_id() . '" width="400" height="300" frameborder="0"></iframe>';
		}
		return implode("\n", $html);
	}
}
?>