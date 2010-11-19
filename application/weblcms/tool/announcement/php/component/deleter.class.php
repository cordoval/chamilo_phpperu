<?php
namespace application\weblcms\tool\announcement;

use application\weblcms\ToolComponent;

class AnnouncementToolDeleterComponent extends AnnouncementTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>