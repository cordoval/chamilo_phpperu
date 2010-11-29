<?php

namespace common\libraries;

/**
 * A rest message renderer to json format
 */

class PlainRestMessageRenderer extends RestMessageRenderer
{
    function render(DataClass $object)
    {
        header('Content-Type: text/plain');
        $plain = array();

        foreach($object->get_default_properties() as $property => $value)
        {
            $plain[] = $property . ' = ' . $value;
        }

        echo implode("\n", $plain);

    }
}

?>