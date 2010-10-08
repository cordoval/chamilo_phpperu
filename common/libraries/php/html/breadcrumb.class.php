<?php
/**
 * $Id: breadcrumb.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html
 */
class Breadcrumb
{
    private $url;
    private $name;

    function Breadcrumb($url, $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

    function get_url()
    {
        return $this->url;
    }

    function get_name()
    {
        return $this->name;
    }

    function set_url($url)
    {
        $this->url = $url;
    }

    function set_name($name)
    {
        $this->name = $name;
    }
}
?>