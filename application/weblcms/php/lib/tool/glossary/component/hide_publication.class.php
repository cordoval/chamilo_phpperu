<?php
class GlossaryToolHidePublicationComponent extends GlossaryTool
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