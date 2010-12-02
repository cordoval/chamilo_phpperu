<?php
namespace application\weblcms\tool\video_conferencing;

use application\weblcms\ToolComponent;

class VideoConferencingToolDeleterComponent extends VideoConferencingTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>