<?php
namespace application\weblcms\tool\geolocation;

use application\weblcms\ToolComponent;

class GeolocationToolShowPublicationComponent extends GeolocationTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

}
?>