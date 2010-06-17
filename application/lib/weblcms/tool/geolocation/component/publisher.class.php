<?php
/**
 * $Id: geolocation_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation.component
 */

class GeolocationToolPublisherComponent extends GeolocationTool
{

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>