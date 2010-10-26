<?php
namespace application\weblcms\tool\geolocation;

use application\weblcms\ToolComponent;

class GeolocationToolHidePublicationComponent extends GeolocationTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>