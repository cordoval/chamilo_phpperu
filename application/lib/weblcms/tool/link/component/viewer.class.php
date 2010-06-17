<?php
/**
 * $Id: Link_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.Link.component
 */

class LinkToolViewerComponent extends LinkTool
{
    private $action_bar;

    function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $viewer->run();
    }
}



?>