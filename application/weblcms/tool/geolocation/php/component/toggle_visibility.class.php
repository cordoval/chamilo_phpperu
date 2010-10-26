<?php
namespace application\weblcms\tool\geolocation;

use application\weblcms\ToolComponent;

class GeoLocationToolToggleVisibilityComponent extends GeolocationTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>