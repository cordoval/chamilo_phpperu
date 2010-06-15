<?php
/**
 * $Id: Link_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.Link.component
 */
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/Link_viewer/Link_browser.class.php';

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