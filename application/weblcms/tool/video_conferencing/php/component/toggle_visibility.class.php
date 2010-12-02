<?php
namespace application\weblcms\tool\video_conferencing;

use application\weblcms\ToolComponent;

class VideoConferencingToolToggleVisibilityComponent extends VideoConferencingTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>