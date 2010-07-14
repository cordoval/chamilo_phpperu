<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

class FlickrExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function as_html()
    {
        $object = $this->get_object();

        $html = array();
        $html[] = '<h3>' . $object->get_title() . '</h3>';
        $html[] = $this->get_preview() . '<br/>';

        return implode("\n", $html);
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $size = ($is_thumbnail ? FlickrExternalRepositoryObject :: SIZE_SQUARE : FlickrExternalRepositoryObject :: SIZE_MEDIUM);
        $class =  ($is_thumbnail ? ' class="thumbnail"' : '');

        $html = array();
        $html[] = '<img' . $class . ' src="' . $object->get_url($size) . '" />';
        return implode("\n", $html);
    }
}
?>