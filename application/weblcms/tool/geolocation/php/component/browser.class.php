<?php
namespace application\weblcms\tool\geolocation;

use common\libraries\ResourceManager;
use common\libraries\Path;
use application\weblcms\ToolComponent;
use common\libraries\Translation;

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
        ToolComponent :: launch($this);
    }

    function show_additional_information($browser)
    {
        $publications = $browser->get_publications();

        if (count($publications) > 0)
        {
            $html = array();

            $html[] = '<br /><br /><h3>' . Translation :: get('LocationsSummary') . '</h3>';

            $html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/libraries/resources/javascript/google_maps.js');
            $html[] = '<div id="map_canvas" style="border: 1px solid black; height:500px"></div>';
            $html[] = '<script type="text/javascript">';
            $html[] = 'initialize(8);';

            foreach ($publications as $publication)
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