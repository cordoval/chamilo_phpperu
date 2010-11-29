<?php

namespace common\libraries;

/**
 * A rest message renderer to json format
 */

class JsonRestMessageRenderer extends RestMessageRenderer
{
    function render(DataClass $object)
    {
        header('Content-Type: application/json');
        echo json_encode($object->get_default_properties());
    }
}

?>
