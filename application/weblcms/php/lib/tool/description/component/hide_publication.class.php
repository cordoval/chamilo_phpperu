<?php
class DescriptionToolHidePublicationComponent extends DescriptionTool
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