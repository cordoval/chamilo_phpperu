<?php
/**
 * $Id: physical_location_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.physical_location
 */
/**
 * This class can be used to display physical_locations
 */
class PhysicalLocationDisplay extends ContentObjectDisplay
{

    function get_description()
    {
        $html = parent :: get_description();
        $object = $this->get_content_object();
        $replace = array();

        $replace[] = '<div class="content_object">';
        $replace[] = '<div class="title">';
        $replace[] = $object->get_location();
        $replace[] = '</div>';
        $replace[] = '<div class="description">';
        $replace[] = $this->get_javascript($object);
        $replace[] = '</div>';
        $replace[] = '</div>';

        return str_replace(self :: DESCRIPTION_MARKER, implode("\n", $replace), $html);
    }

    function get_short_html()
    {
        $object = $this->get_content_object();
        return '<span class="content_object">' . htmlentities($object->get_title()) . ' - ' . htmlentities($object->get_location()) . '</span>';
    }

    function get_javascript($object)
    {
        $html = array();

        $html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/google_maps.js');
        $html[] = '<div id="map_canvas" style="width:100%; border: 1px solid black; height:500px"></div>';
        $html[] = '<script type="text/javascript">';
        $html[] = 'initialize(12);';
        $html[] = 'codeAddress(\'' . $object->get_location() . '\', \'' . $object->get_title() . '\');';
        $html[] = '</script>';

        return implode("\n", $html);
    }
}
?>