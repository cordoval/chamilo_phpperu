<?php
namespace application\weblcms\tool\video_conferencing;

use application\weblcms\ToolComponent;

class VideoConferencingToolHidePublicationComponent extends VideoConferencingTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>