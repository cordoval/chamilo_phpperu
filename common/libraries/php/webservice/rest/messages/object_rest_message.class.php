<?php

namespace common\libraries;

/**
 * A rest message that returns a chamilo object (dataclass)
 */

class ObjectRestMessage extends RestMessage
{
    private $object;

    function get_object()
    {
        return $this->object;
    }

    function set_object($object)
    {
        $this->object = $object;
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
