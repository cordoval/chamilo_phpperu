<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

class FlickrExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function as_html()
    {
        $object = $this->get_object();
        $html = array();
        return implode("\n", $html);
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $html = array();
        $html[] = '<img src="' . $object->get_url() . '" />';
        return implode("\n", $html);
    }
}
?>