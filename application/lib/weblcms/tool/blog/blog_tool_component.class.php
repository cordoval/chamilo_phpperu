<?php
/**
 * $Id: blog_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.blog
 */
require_once dirname(__FILE__) . '/blog_tool.class.php';

class BlogToolComponent extends ToolComponent
{

    static function factory($component_name, $learning_path_tool)
    {
        return parent :: factory('Blog', $component_name, $learning_path_tool);
    }

    function display_content_object($object, $cloi_id)
    {
        return $this->get_tool()->display_content_object($object, $cloi_id);
    }

    function retrieve_feedback($cloi_id)
    {
        return $this->get_tool()->retrieve_feedback($cloi_id);
    }

    function count_feedback($cloi_id)
    {
        return $this->get_tool()->retrieve_feedback($cloi_id);
    }

}
?>