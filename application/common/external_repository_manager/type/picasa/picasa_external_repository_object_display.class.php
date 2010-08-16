<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

class PicasaExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
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
        $size = ($is_thumbnail ? PicasaExternalRepositoryObject :: SIZE_THUMBNAIL_MEDIUM : PicasaExternalRepositoryObject :: SIZE_THUMBNAIL_LARGE);
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');

        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $object->get_url($size) . '" />';
        return implode("\n", $html);
    }
}
?>