<?php
namespace common\extensions\external_repository_manager\implementation\wikimedia;

use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

use common\libraries\Translation;

class WikimediaExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_display_properties()
    {
        $object = $this->get_object();

        $properties = parent :: get_display_properties();
        $properties[Translation :: get('AvailableSizes')] = $object->get_available_sizes_string();

        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $size = ($is_thumbnail ? WikimediaExternalRepositoryObject :: SIZE_THUMBNAIL : WikimediaExternalRepositoryObject :: SIZE_MEDIUM);
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');

        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $object->get_url($size) . '" />';
        return implode("\n", $html);
    }
}
?>