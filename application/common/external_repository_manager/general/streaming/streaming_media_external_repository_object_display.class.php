<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

abstract class StreamingMediaExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' . $object->get_duration() . ')</h3>';
    }
}
?>