<?php

namespace common\libraries;

/**
 * A rest message that returns multiple chamilo objects (dataclasses)
 */

class ObjectsRestMessage extends RestMessage
{
    private $objects;

    function get_objects()
    {
        return $this->objects;
    }

    function set_object($objects)
    {
        $this->objects = $objects;
    }

    function render_as_xml()
    {

    }

    function render_as_html()
    {

    }

    function render_as_json()
    {

    }

    function render_as_plain()
    {

    }
}

?>
