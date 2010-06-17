<?php
/**
 * $Id: geolocation_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation.component
 */

class GeolocationToolBrowserComponent extends GeolocationTool
{
    private $introduction_text;

    function run()
    {
        $browser = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $browser->run();
    }

    function get_browser_type()
    {
        return ContentObjectPublicationListRenderer :: TYPE_TABLE;
    }

    function get_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        return $browser_types;
    }
}
?>