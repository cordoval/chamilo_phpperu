<?php
namespace common\extensions\external_repository_manager\implementation\vimeo;

use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

use common\libraries\Translation;

class VimeoExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_display_properties()
    {
        $object = $this->get_object();

        $properties = parent :: get_display_properties();
        $properties[Translation :: get('AvailableSizes')] = $object->get_available_sizes_string();
        $properties[Translation :: get('Tags')] = $object->get_tags_string();
        $properties[Translation :: get('License')] = $object->get_license_icon();

        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $size = ($is_thumbnail ? VimeoExternalRepositoryObject :: SIZE_SQUARE : VimeoExternalRepositoryObject :: SIZE_MEDIUM);
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');

        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $object->get_url($size) . '" />';
        return implode("\n", $html);
    }
}
?>