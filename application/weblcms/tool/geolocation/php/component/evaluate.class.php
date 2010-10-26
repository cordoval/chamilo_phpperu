<?php
namespace application\weblcms\tool\geolocation;

use application\weblcms\ToolComponent;

class GeolocationToolEvaluateComponent extends GeolocationTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>