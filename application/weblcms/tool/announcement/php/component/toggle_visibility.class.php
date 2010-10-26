<?php
namespace application\weblcms\tool\announcement;

use application\weblcms\ToolComponent;

class AnnouncementToolToggleVisibilityComponent extends AnnouncementTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>