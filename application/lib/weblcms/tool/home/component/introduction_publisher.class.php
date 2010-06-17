<?php
/**
 * $Id: introduction_publisher.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */

class HomeToolIntroductionPublisherComponent extends HomeTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: INTRODUCTION_PUBLISHER_COMPONENT, $this);
        $component->run();
    }
}
?>