<?php
namespace application\weblcms\tool\streaming_video;

use application\weblcms\ToolComponent;

class StreamingVideoToolShowPublicationComponent extends StreamingVideoTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>