<?php
/**
 * $Id: object_table_form_action.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */
class ObjectTableFormAction
{
    private $action;
    private $title;
    private $confirm;

    function ObjectTableFormAction($action, $title, $confirm = true)
    {
        $this->action = $action;
        $this->title = $title;
        $this->confirm = $confirm;
    }

    function get_action()
    {
        return $this->action;
    }

    function get_title()
    {
        return $this->title;
    }

    function get_confirm()
    {
        return $this->confirm;
    }

    function set_action($action)
    {
        $this->action = $action;
    }

    function set_title($title)
    {
        $this->title = $title;
    }

    function set_confirm($confirm)
    {
        $this->confirm = $confirm;
    }
}
?>