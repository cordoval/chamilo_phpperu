<?php
namespace application\weblcms\tool\announcement;

use application\weblcms\ToolComponent;

class AnnouncementToolEvaluateComponent extends AnnouncementTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>