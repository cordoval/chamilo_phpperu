<?php
/**
 * $Id: foprm_tab.class.php 6 2010-03-03 9:30:20Z tristan $
 * @package common.html.formvalidator
 */
class FormTab
{
    private $method;
    private $title;

    function FormTab($method, $title)
    {
        $this->method = $method;
        $this->title = $title;
    }

    function get_method()
    {
        return $this->method;
    }

    function get_title()
    {
        return $this->title;
    }

    function set_method($method)
    {
        $this->method = $method;
    }

    function set_title($title)
    {
        $this->title = $title;
    }
}
?>