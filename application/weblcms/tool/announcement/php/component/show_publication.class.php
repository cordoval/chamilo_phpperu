<?php
namespace application\weblcms\tool\announcement;

use application\weblcms\ToolComponent;

class AnnouncementToolShowPublicationComponent extends AnnouncementTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

}
?>