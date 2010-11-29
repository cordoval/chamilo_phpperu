<?php

namespace common\libraries;

/**
 * A rest message renderer to json format
 */

class JsonRestMessageRenderer extends RestMessageRenderer
{
    function render_object(DataClass $object)
    {
        $this->render_array($object->get_default_properties());
    }

    function render_multiple_objects(array $objects)
    {
        $array = array();

        foreach($objects as $object)
        {
            $array[] = $object->get_default_properties();
        }

        $this->render_array($array);
    }

    private function render_array(array $array)
    {
        header('Content-Type: application/json');
        echo json_encode($array);
    }
}

?>
