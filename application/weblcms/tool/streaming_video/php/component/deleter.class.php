<?php
namespace application\weblcms\tool\streaming_video;

use application\weblcms\ToolComponent;

class StreamingVideoToolDeleterComponent extends StreamingVideoTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>