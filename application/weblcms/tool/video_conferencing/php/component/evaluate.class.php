<?php
namespace application\weblcms\tool\video_conferencing;

use application\weblcms\ToolComponent;

class VideoConferencingToolEvaluateComponent extends VideoConferencingTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>