<?php
/**
 * $Id: blog_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.blog
 */

require_once dirname(__FILE__) . '/blog_tool_component.class.php';
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
class BlogTool extends Tool
{
    const ACTION_VIEW_BLOGS = 'view';
    
    const PARAM_BLOG = 'blog';

    // Inherited.
    function run()
    {
        $action = $this->get_action();
        $component = parent :: run();
        
        if ($component)
            return;
        
        switch ($action)
        {
            case self :: ACTION_PUBLISH :
                $component = BlogToolComponent :: factory('Publisher', $this);
                break;
            case self :: ACTION_VIEW_BLOGS :
                $component = BlogToolComponent :: factory('Viewer', $this);
                break;
            default :
                $component = BlogToolComponent :: factory('Viewer', $this);
                break;
        }
        
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(BlogItem :: get_type_name());
    }
}
?>