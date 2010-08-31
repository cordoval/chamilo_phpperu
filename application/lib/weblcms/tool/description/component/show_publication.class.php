<?php
class DescriptionToolShowPublicationComponent extends DescriptionTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_hidden()
    {
        return 0;
    }
}
?>