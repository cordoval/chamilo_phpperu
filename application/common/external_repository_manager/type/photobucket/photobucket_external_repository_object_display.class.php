<?php
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_external_repository_object_display.class.php';

/**
 *
 * @author magali.gillard
 *
 */
class PhotobucketExternalRepositoryObjectDisplay extends StreamingMediaExternalRepositoryObjectDisplay
{
    function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . '</h3>';
    }

    function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Album')] = $this->get_object()->get_album_string();
        $properties[Translation :: get('Tags')] = $this->get_object()->get_tags_string();
        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $html = array();
        $html[] = '<embed height="344" width="425" type="application/x-shockwave-flash" src="' . $object->get_url() . '"></embed>';
        return implode("\n", $html);
    }
}
?>