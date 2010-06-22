<?php
/**
 * $Id: wiki_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.wiki.component
 */

class WikiToolComplexDisplayComponent extends WikiTool
{
	function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: DISPLAY_COMPLEX_CONTENT_OBJECT_COMPONENT, $this);
        $viewer->run();
    }
}
?>