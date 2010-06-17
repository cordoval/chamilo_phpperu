<?php
/**
 * $Id: geolocation_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation.component
 */

class GeolocationToolBrowserComponent extends GeolocationTool
{
    private $introduction_text;
    private $publications;

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
    
    function show_additional_information($browser)
    {
    	$publications = $browser->get_publications();
    	
        if (count($publications) > 0)
        {
            $html = array();

            $html[] = '<br /><br /><h3>' . Translation :: get('LocationsSummary') . '</h3>';

            $html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/google_maps.js');
            $html[] = '<div id="map_canvas" style="border: 1px solid black; height:500px"></div>';
            $html[] = '<script type="text/javascript">';
            $html[] = 'initialize(8);';

            foreach($publications as $publication)
            {
                if ($publication->is_visible_for_target_users())
                {
                    $html[] = 'codeAddress(\'' . $publication->get_content_object()->get_location() . '\', \'' . $publication->get_content_object()->get_title() . '\');';
                }
            }
            $html[] = '</script>';
            echo implode("\n", $html);
        }
    }
}
?>