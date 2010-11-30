<?php

namespace common\libraries;

/**
 * A rest message renderer to json format
 */

class XmlRestMessageRenderer extends RestMessageRenderer
{
    function render_object(DataClass $object)
    {
        $this->render_xml_header();
        echo $this->render_object_body($object);
    }

    function render_multiple_objects(array $objects)
    {
        $this->render_xml_header();

        $xml = array();
        $xml[] = '<objects>';

        foreach($objects as $object)
        {
            $xml[] = $this->render_object_body($object);
        }

        $xml[] = '</objects>';
        echo implode("\n", $xml);

    }

    private function render_object_body(DataClass $object)
    {
        $xml = array();
        $xml[] = '<object>';

        foreach($object->get_default_properties() as $name => $value)
        {
            $xml[] = '<' . $name . '>' . $value . '</' . $name . '>';
        }

        $xml[] = '</object>';
        return implode("\n", $xml);
    }

    private function render_xml_header()
    {
        header('Content-Type: application/xml');

        $xml = array();
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';

        echo implode("\n", $xml);
    }
}

?>