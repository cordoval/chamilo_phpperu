<?php
class LearningPathToolHidePublicationComponent extends LearningPathTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_hidden()
    {
        return 1;
    }
}
?>