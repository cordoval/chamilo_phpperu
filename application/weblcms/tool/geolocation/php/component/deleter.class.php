<?php
namespace application\weblcms\tool\geolocation;

use application\weblcms\ToolComponent;

class GeolocationToolDeleterComponent extends GeolocationTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>