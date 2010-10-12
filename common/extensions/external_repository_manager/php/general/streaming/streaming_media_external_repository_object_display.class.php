<?php
namespace common\extensions\external_repository_manager;
require_once Path :: get_common_extensions_path() . 'external_repository_manager/php/external_repository_object_display.class.php';

abstract class StreamingMediaExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' . $object->get_duration() . ')</h3>';
    }
}
?>