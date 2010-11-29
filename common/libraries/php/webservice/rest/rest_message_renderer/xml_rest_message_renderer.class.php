<?php

namespace common\libraries;

/**
 * A rest message renderer to json format
 */

class XmlRestMessageRenderer extends RestMessageRenderer
{
    function render(DataClass $object)
    {
        header('Content-Type: application/xml');

        $xml = array();
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<object>';

        foreach($object->get_default_properties() as $name => $value)
        {
            $xml[] = '<' . $name . '>' . $value . '</' . $name . '>';
        }
        
        $xml[] = '</object>';
        echo implode("\n", $xml);

    }
}

?>