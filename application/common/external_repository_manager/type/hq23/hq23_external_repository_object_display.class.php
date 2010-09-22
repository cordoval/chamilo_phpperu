<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

class Hq23ExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_display_properties()
    {
        $object = $this->get_object();

        $properties = parent :: get_display_properties();
		$properties[Translation :: get('Album')] = Translation:: get($object->get_album_name());
        $properties[Translation :: get('Tags')] = $object->get_tags_string();

        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $size = ($is_thumbnail ? Hq23ExternalRepositoryObject :: SIZE_SQUARE : Hq23ExternalRepositoryObject :: SIZE_MEDIUM);
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');

        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $object->get_url() . '" />';
        return implode("\n", $html);
    }
}
?>