<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

abstract class StreamingMediaExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' . $object->get_duration() . ')</h3>';
    }

    function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Status')] = $this->get_object()->get_status_text();
        return $properties;
    }
}
?>