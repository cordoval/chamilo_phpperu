<?php
/**
 * $Id: description_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.description.component
 */
require_once dirname(__FILE__) . '/../description_tool.class.php';
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class DescriptionToolPublisherComponent extends DescriptionTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>