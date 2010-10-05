<?php
class AnnouncementToolHidePublicationComponent extends AnnouncementTool
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