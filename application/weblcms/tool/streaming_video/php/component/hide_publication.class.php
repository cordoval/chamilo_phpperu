<?php
namespace application\weblcms\tool\streaming_video;

use application\weblcms\ToolComponent;

class StreamingVideoToolHidePublicationComponent extends StreamingVideoTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>