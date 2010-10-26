<?php
namespace application\weblcms\tool\announcement;

use application\weblcms\ToolComponent;

class AnnouncementToolHidePublicationComponent extends AnnouncementTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>