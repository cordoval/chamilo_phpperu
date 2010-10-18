<?php
class GeolocationToolHidePublicationComponent extends GeolocationTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_hidden()
    {
        return 1;
    }
}
?>