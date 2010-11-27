<?php

namespace common\libraries;

/**
 * A rest message that has a true or false to determine wether a manipulation action has been successfull
 */

class SuccessRestMessage extends RestMessage
{
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
        return $this->get_success() ? Translation :: get('ConfirmTrue') : Translation :: get('ConfirmFalse');
    }

    function get_message()
    {
        return $this->message;
    }

    function set_message($message)
    {
        $this->message = $message;
    }

    function render_as_xml()
    {
        parent :: render_xml_header();
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

    }

    function render_as_plain()
    {
        
    }
}

?>
