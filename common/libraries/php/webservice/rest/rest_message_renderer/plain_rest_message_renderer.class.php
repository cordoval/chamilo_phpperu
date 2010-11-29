<?php

namespace common\libraries;

/**
 * A rest message renderer to json format
 */

class PlainRestMessageRenderer extends RestMessageRenderer
{
    function render_object(DataClass $object)
    {
        $this->render_header();
        echo $this->render_object_body($object);
    }

    function render_multiple_objects(array $objects)
    {
        $this->render_header();

        $plain = array();

        foreach($objects as $object)
        {
            $plain[] = 'Object';
            $plain[] = $this->render_object_body($object);
            $plain[] = '';
        }

        echo implode("\n", $plain);
    }

    private function render_object_body(DataClass $object)
    {
        $plain = array();

        foreach($object->get_default_properties() as $property => $value)
        {
            $plain[] = $property . ' = ' . $value;
        }

        return implode("\n", $plain);
    }

    private function render_header()
    {
        header('Content-Type: text/plain');
    }
}

?>