<?php
class StreamingVideoToolShowPublicationComponent extends StreamingVideoTool
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