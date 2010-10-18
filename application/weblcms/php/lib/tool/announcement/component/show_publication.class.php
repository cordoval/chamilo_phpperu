<?php
class AnnouncementToolShowPublicationComponent extends AnnouncementTool
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