<?php
class BlogToolShowPublicationComponent extends BlogTool
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