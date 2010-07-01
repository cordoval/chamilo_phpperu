<?php
/**
 * $Id: blog_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.blog.component
 */


/**
 * Represents the view component for the assessment tool.
 *
 */
class BlogToolComplexDisplayComponent extends BlogTool
{
	function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: DISPLAY_COMPLEX_CONTENT_OBJECT_COMPONENT, $this);
        $viewer->run();
    }
}
?>