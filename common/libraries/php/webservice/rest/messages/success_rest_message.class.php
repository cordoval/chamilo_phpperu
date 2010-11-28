<?php

namespace common\libraries;

/**
 * A rest message that has a true or false to determine wether a manipulation action has been successfull
 */

class SuccessRestMessage extends RestMessage
{
    const PROPERTY_SUCCESS = 'success';
    const PROPERTY_MESSAGE = 'message';

    private $success;
    private $message;

    function get_success()
    {
        return $this->success;
    }

    function set_success($success)
    {
        $this->success = $success;
    }

    function get_success_as_string()
    {
        return $this->get_success() ? 'true' : 'false';
    }

    function get_message()
    {
        return $this->message;
    }

    function set_message($message)
    {
        $this->message = $message;
    }

    function as_array()
    {
        return array(self :: PROPERTY_SUCCESS => $this->get_success_as_string(), self :: PROPERTY_MESSAGE => $this->get_message());
    }

    function render_as_xml()
    {
        parent :: render_xml_header();

        $xml = array();
        $xml[] = '<success>';
        $xml[] = '<value>' . $this->get_success_as_string() . '</value>';
        $xml[] = '<message>' . $this->get_message() . '</message>';
        $xml[] = '</success>';
        echo implode("\n", $xml);
    }

    function render_as_html()
    {
        parent :: render_html_header(array('&nbsp;', '&nbsp;'));

        $html[] = '<tr>';
        $html[] = '<td>' . Translation :: get('Success') . '</td><td>' . $this->get_success_as_string() . '</td>';
        $html[] = '</tr><tr class="row_odd">';
        $html[] = '<td>' . Translation :: get('Message') . '</td><td>' . $this->get_message() . '</td>';
        $html[] = '</tr>';
        echo implode("\n", $html);

        parent :: render_html_footer();
    }

    function render_as_json()
    {
        echo json_encode($this->as_array());
    }

    function render_as_plain()
    {
        $plain = array();

        foreach($this->as_array() as $property => $value)
        {
            $plain[] = $property . ' = ' . $value;
        }

        echo implode("\n", $plain);
    }
}

?>
